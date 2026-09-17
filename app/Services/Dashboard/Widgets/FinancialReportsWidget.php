<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchMonthlyCost;
use App\Models\EmployeePayroll;
use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class FinancialReportsWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'financial_reports';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        // YTD summary
        $ytdRevenue = MemberPayment::where('payment_status', 'paid')
            ->whereYear('paid_at', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $ytdCost = BranchMonthlyCost::where('period_year', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $ytdPayroll = EmployeePayroll::where('payment_status', 'paid')
            ->whereYear('paid_at', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->sum('net_amount');

        $ytdProfit = $ytdRevenue - $ytdCost - $ytdPayroll;

        // Monthly breakdown for chart
        $monthlyRevenue = collect(range(1, $now->month))->map(fn ($m) => [
            'month' => $m,
            'revenue' => MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $m)
                ->whereYear('paid_at', $now->year)
                ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
                ->sum('amount'),
        ])->toArray();

        return [
            'ytd_revenue' => $ytdRevenue,
            'ytd_cost' => $ytdCost + $ytdPayroll,
            'ytd_profit' => $ytdProfit,
            'monthly_revenue' => $monthlyRevenue,
            'year' => $now->year,
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        return collect(range(1, $now->month))->map(function ($m) use ($now, $branchId) {
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $m)
                ->whereYear('paid_at', $now->year)
                ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
                ->sum('amount');

            $cost = BranchMonthlyCost::where('period_month', $m)
                ->where('period_year', $now->year)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->sum('amount');

            return [
                'month' => $m,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $revenue - $cost,
            ];
        })->toArray();
    }
}
