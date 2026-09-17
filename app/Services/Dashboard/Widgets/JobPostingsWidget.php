<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class JobPostingsWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'job_postings';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $query = JobPosting::when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $totalPostings = (clone $query)->count();

        $byStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $currentMonthPostings = JobPosting::whereMonth('publish_date', $currentMonth)
            ->whereYear('publish_date', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $closingSoon = JobPosting::where('status', 'published')
            ->whereBetween('closing_date', [$now, $now->copy()->addDays(7)])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $totalApplications = JobApplication::whereMonth('applied_at', $currentMonth)
            ->whereYear('applied_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->count();

        return [
            'total_postings' => $totalPostings,
            'by_status' => $byStatus,
            'new_this_month' => $currentMonthPostings,
            'closing_soon' => $closingSoon,
            'total_applications' => $totalApplications,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $postings = JobPosting::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with(['branch', 'position'])
            ->get();

        return $postings->map(fn ($p) => [
            'title' => $p->title,
            'branch' => $p->branch?->branch_name ?? '-',
            'position' => $p->position?->position_name ?? '-',
            'status' => $p->status,
            'publish_date' => $p->publish_date?->format('Y-m-d'),
            'closing_date' => $p->closing_date?->format('Y-m-d'),
            'applications_count' => $p->applications()->count(),
        ])->toArray();
    }
}
