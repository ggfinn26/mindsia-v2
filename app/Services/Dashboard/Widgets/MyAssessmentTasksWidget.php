<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberSessionAssessment;
use App\Models\SessionSchedule;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyAssessmentTasksWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_assessment_tasks';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        // Sessions where assessment is pending (session done but not scored)
        $sessionIds = SessionSchedule::where('tutor_employee_id', $employeeId)
            ->where('status', 'completed')
            ->pluck('id');

        $pendingAssessments = MemberSessionAssessment::whereIn('session_schedule_id', $sessionIds)
            ->whereNull('score')
            ->count();

        $submittedThisMonth = MemberSessionAssessment::whereIn('session_schedule_id', $sessionIds)
            ->whereNotNull('score')
            ->whereMonth('updated_at', $now->month)
            ->whereYear('updated_at', $now->year)
            ->count();

        $totalPending = $pendingAssessments;

        return [
            'pending_count' => $pendingAssessments,
            'submitted_this_month' => $submittedThisMonth,
            'total_sessions_needing_assessment' => $totalPending,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        $sessionIds = SessionSchedule::where('tutor_employee_id', $employeeId)
            ->pluck('id');

        return MemberSessionAssessment::whereIn('session_schedule_id', $sessionIds)
            ->with('member:id,full_name', 'sessionSchedule:id,session_date')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn ($a) => [
                'member' => $a->member?->full_name ?? '-',
                'session_date' => $a->sessionSchedule?->session_date?->format('Y-m-d'),
                'score' => $a->score ?? 'Pending',
            ])->toArray();
    }
}
