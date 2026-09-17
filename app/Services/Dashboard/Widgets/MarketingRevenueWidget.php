<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MarketingPerformance;
use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MarketingRevenueWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'marketing_revenue';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $revenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $prevMonth = $now->copy()->subMonth();
        $prevRevenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prevMonth->month)
            ->whereYear('paid_at', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $trend = $prevRevenue > 0
            ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : 0;

        $byEmployee = MarketingPerformance::whereMonth('period_month', $currentMonth)
            ->whereYear('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('employee:id,full_name')
            ->get()
            ->map(fn ($mp) => [
                'employee' => $mp->employee?->full_name ?? '-',
                'amount' => $mp->total_revenue ?? 0,
            ])
            ->sortByDesc('amount')
            ->values()
            ->take(5)
            ->toArray();

        return [
            'total_revenue' => $revenue,
            'prev_revenue' => $prevRevenue,
            'trend_percent' => $trend,
            'by_employee' => $byEmployee,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $performances = MarketingPerformance::whereYear('period_year', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('employee:id,full_name')
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->get();

        return $performances->map(fn ($mp) => [
            'employee' => $mp->employee?->full_name ?? '-',
            'period' => $mp->period_month.'/'.$mp->period_year,
            'total_revenue' => $mp->total_revenue ?? 0,
        ])->toArray();
    }
}
