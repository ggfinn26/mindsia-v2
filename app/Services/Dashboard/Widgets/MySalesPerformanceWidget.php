<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MarketingPerformance;
use App\Models\MarketingTarget;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MySalesPerformanceWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_sales_performance';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $performance = MarketingPerformance::where('employee_id', $employeeId)
            ->where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->first();

        $target = MarketingTarget::where('employee_id', $employeeId)
            ->where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->first();

        $totalMember = $performance?->total_member ?? 0;
        $targetMember = $target?->target_member ?? 0;
        $achievementPercent = $targetMember > 0
            ? round(($totalMember / $targetMember) * 100, 1)
            : null;

        $totalRevenue = $performance?->total_revenue ?? 0;

        return [
            'total_member' => $totalMember,
            'target_member' => $targetMember,
            'achievement_percent' => $achievementPercent,
            'total_revenue' => $totalRevenue,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return MarketingPerformance::where('employee_id', $employeeId)
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->get()
            ->map(fn ($mp) => [
                'period' => $mp->period_month.'/'.$mp->period_year,
                'total_member' => $mp->total_member ?? 0,
                'total_revenue' => $mp->total_revenue ?? 0,
            ])->toArray();
    }
}
