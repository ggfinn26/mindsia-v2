<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchMonthlyCost;
use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class FinancialForecastingWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'financial_forecasting';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        // Collect last 3 months of revenue for simple average forecast
        $monthlyRevenue = collect(range(1, 3))->map(function ($i) use ($now, $branchId) {
            $period = $now->copy()->subMonths($i);

            return MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $period->month)
                ->whereYear('paid_at', $period->year)
                ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
                ->sum('amount');
        });

        $avgRevenue = $monthlyRevenue->avg();

        // Collect last 3 months of cost
        $monthlyCost = collect(range(1, 3))->map(function ($i) use ($now, $branchId) {
            $period = $now->copy()->subMonths($i);

            return BranchMonthlyCost::where('period_month', $period->month)
                ->where('period_year', $period->year)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->sum('amount');
        });

        $avgCost = $monthlyCost->avg();

        $forecastRevenue = round($avgRevenue);
        $forecastCost = round($avgCost);
        $forecastMargin = $forecastRevenue - $forecastCost;

        // History for chart (last 3 months actual)
        $history = collect(range(3, 1))->map(function ($i) use ($now, $branchId) {
            $period = $now->copy()->subMonths($i);
            $rev = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $period->month)
                ->whereYear('paid_at', $period->year)
                ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
                ->sum('amount');

            return [
                'period' => $period->translatedFormat('M Y'),
                'revenue' => $rev,
            ];
        })->toArray();

        return [
            'forecast_revenue' => $forecastRevenue,
            'forecast_cost' => $forecastCost,
            'forecast_margin' => $forecastMargin,
            'history' => $history,
            'next_period' => $now->copy()->addMonth()->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        return collect(range(6, 1))->map(function ($i) use ($now, $branchId) {
            $period = $now->copy()->subMonths($i);
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $period->month)
                ->whereYear('paid_at', $period->year)
                ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
                ->sum('amount');

            $cost = BranchMonthlyCost::where('period_month', $period->month)
                ->where('period_year', $period->year)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->sum('amount');

            return [
                'period' => $period->format('M Y'),
                'revenue' => $revenue,
                'cost' => $cost,
                'margin' => $revenue - $cost,
            ];
        })->toArray();
    }
}
