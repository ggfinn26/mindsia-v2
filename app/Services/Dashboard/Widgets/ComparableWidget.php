<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchMonthlyCost;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class ComparableWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'comparable';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        // Period over period: this month vs last month
        $thisMonth = $this->periodMetrics($now->month, $now->year, $branchId);
        $lastMonth = $this->periodMetrics($now->copy()->subMonth()->month, $now->copy()->subMonth()->year, $branchId);

        // YoY: this month vs same month last year
        $sameMonthLastYear = $this->periodMetrics($now->month, $now->year - 1, $branchId);

        return [
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'same_month_last_year' => $sameMonthLastYear,
            'mom_revenue_change' => $lastMonth['revenue'] > 0
                ? round((($thisMonth['revenue'] - $lastMonth['revenue']) / $lastMonth['revenue']) * 100, 1)
                : 0,
            'yoy_revenue_change' => $sameMonthLastYear['revenue'] > 0
                ? round((($thisMonth['revenue'] - $sameMonthLastYear['revenue']) / $sameMonthLastYear['revenue']) * 100, 1)
                : 0,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    /** @return array{revenue: int, members: int, cost: int} */
    private function periodMetrics(int $month, int $year, ?int $branchId): array
    {
        $revenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $month)
            ->whereYear('paid_at', $year)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $members = MemberRegistration::whereMonth('registration_date', $month)
            ->whereYear('registration_date', $year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $cost = BranchMonthlyCost::where('period_month', $month)
            ->where('period_year', $year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        return compact('revenue', 'members', 'cost');
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        return collect(range(0, 5))->map(function ($i) use ($now, $branchId) {
            $period = $now->copy()->subMonths($i);
            $metrics = $this->periodMetrics($period->month, $period->year, $branchId);

            return [
                'period' => $period->format('M Y'),
                'revenue' => $metrics['revenue'],
                'members' => $metrics['members'],
                'cost' => $metrics['cost'],
            ];
        })->toArray();
    }
}
