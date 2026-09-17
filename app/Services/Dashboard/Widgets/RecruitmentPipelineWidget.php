<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\JobApplication;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class RecruitmentPipelineWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'recruitment_pipeline';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $query = JobApplication::whereMonth('applied_at', $currentMonth)
            ->whereYear('applied_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('jobPosting', fn ($j) => $j->where('branch_id', $branchId)));

        $totalApplications = (clone $query)->count();

        $byStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $hired = $byStatus['hired'] ?? 0;
        $rejected = $byStatus['rejected'] ?? 0;

        $conversionRate = $totalApplications > 0
            ? round(($hired / $totalApplications) * 100, 1)
            : 0;

        $prevMonth = $now->copy()->subMonth();
        $prevTotal = JobApplication::whereMonth('applied_at', $prevMonth->month)
            ->whereYear('applied_at', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->whereHas('jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->count();

        $trend = $prevTotal > 0
            ? round((($totalApplications - $prevTotal) / $prevTotal) * 100, 1)
            : 0;

        return [
            'total_applications' => $totalApplications,
            'by_status' => $byStatus,
            'hired' => $hired,
            'rejected' => $rejected,
            'conversion_rate' => $conversionRate,
            'trend_percent' => $trend,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $applications = JobApplication::whereYear('applied_at', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->with(['applicant', 'jobPosting'])
            ->get();

        return $applications->map(fn ($a) => [
            'applicant' => $a->applicant?->full_name ?? '-',
            'position' => $a->jobPosting?->title ?? '-',
            'source' => $a->application_source,
            'status' => $a->status,
            'applied_at' => $a->applied_at?->format('Y-m-d'),
        ])->toArray();
    }
}
