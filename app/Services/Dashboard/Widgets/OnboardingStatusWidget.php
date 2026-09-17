<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeOnboarding;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class OnboardingStatusWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'onboarding_status';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $query = EmployeeOnboarding::when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $total = (clone $query)->count();

        $byStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $completedThisMonth = EmployeeOnboarding::where('status', 'completed')
            ->whereMonth('completed_at', $currentMonth)
            ->whereYear('completed_at', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $pendingReview = EmployeeOnboarding::where('status', 'pending_review')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $rejectedThisMonth = EmployeeOnboarding::where('status', 'rejected')
            ->whereMonth('rejected_at', $currentMonth)
            ->whereYear('rejected_at', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        return [
            'total' => $total,
            'by_status' => $byStatus,
            'completed_this_month' => $completedThisMonth,
            'pending_review' => $pendingReview,
            'rejected_this_month' => $rejectedThisMonth,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $onboardings = EmployeeOnboarding::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with(['application.applicant', 'position', 'branch'])
            ->get();

        return $onboardings->map(fn ($o) => [
            'applicant' => $o->application?->applicant?->full_name ?? '-',
            'position' => $o->position?->position_name ?? '-',
            'branch' => $o->branch?->branch_name ?? '-',
            'employment_type' => $o->employment_type ?? '-',
            'start_date' => $o->start_date?->format('Y-m-d') ?? '-',
            'status' => $o->status,
            'completed_at' => $o->completed_at?->format('Y-m-d') ?? '-',
        ])->toArray();
    }
}
