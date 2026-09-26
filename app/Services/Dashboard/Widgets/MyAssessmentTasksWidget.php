<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberSessionAssessment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyAssessmentTasksWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_assessment_tasks';
    }

    public function getData(array $filters = []): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $pastSessionsScope = fn ($q) => $q->where('schedule_date', '<', $now->toDateString());

        $pendingAssessments = MemberSessionAssessment::where('employee_id', $employeeId)
            ->whereHas('classSchedule', $pastSessionsScope)
            ->whereNull('score')
            ->count();

        $submittedThisMonth = MemberSessionAssessment::where('employee_id', $employeeId)
            ->whereNotNull('score')
            ->whereMonth('updated_at', $now->month)
            ->whereYear('updated_at', $now->year)
            ->count();

        return [
            'pending_count' => $pendingAssessments,
            'submitted_this_month' => $submittedThisMonth,
            'total_sessions_needing_assessment' => $pendingAssessments,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return MemberSessionAssessment::where('employee_id', $employeeId)
            ->with('classSchedule:id,schedule_date', 'memberClass.memberData:id,full_name')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn ($a) => [
                'member' => $a->memberClass?->memberData?->full_name ?? '-',
                'schedule_date' => $a->classSchedule?->schedule_date?->format('Y-m-d'),
                'score' => $a->score ?? 'Pending',
            ])->toArray();
    }
}
