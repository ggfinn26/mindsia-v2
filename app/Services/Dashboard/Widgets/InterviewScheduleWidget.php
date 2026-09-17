<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\ApplicantInterviewSchedule;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class InterviewScheduleWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'interview_schedule';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $query = ApplicantInterviewSchedule::whereMonth('scheduled_at', $currentMonth)
            ->whereYear('scheduled_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('application.jobPosting', fn ($j) => $j->where('branch_id', $branchId)));

        $totalInterviews = (clone $query)->count();

        $byType = (clone $query)
            ->selectRaw('interview_type, COUNT(*) as count')
            ->groupBy('interview_type')
            ->pluck('count', 'interview_type')
            ->toArray();

        $upcoming = ApplicantInterviewSchedule::where('scheduled_at', '>=', $now)
            ->when($branchId, fn ($q) => $q->whereHas('application.jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->count();

        $completed = (clone $query)->whereHas('evaluation')->count();

        $byDecision = ApplicantInterviewSchedule::whereHas('evaluation')
            ->whereMonth('scheduled_at', $currentMonth)
            ->whereYear('scheduled_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('application.jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->selectRaw('decision, COUNT(*) as count')
            ->groupBy('decision')
            ->pluck('count', 'decision')
            ->toArray();

        return [
            'total_interviews' => $totalInterviews,
            'by_type' => $byType,
            'upcoming' => $upcoming,
            'completed' => $completed,
            'by_decision' => $byDecision,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $schedules = ApplicantInterviewSchedule::whereYear('scheduled_at', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('application.jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->with(['application.applicant', 'interviewer', 'evaluation'])
            ->get();

        return $schedules->map(fn ($s) => [
            'applicant' => $s->application?->applicant?->full_name ?? '-',
            'interview_type' => $s->interview_type,
            'scheduled_at' => $s->scheduled_at?->format('Y-m-d H:i'),
            'interviewer' => $s->interviewer?->full_name ?? '-',
            'decision' => $s->evaluation?->decision ?? '-',
            'total_score' => $s->evaluation?->total_score ?? '-',
        ])->toArray();
    }
}
