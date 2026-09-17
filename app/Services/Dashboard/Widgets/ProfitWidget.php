<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchMonthlyCost;
use App\Models\EmployeePayroll;
use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class ProfitWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'profit';
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

        $payrollCost = EmployeePayroll::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('total_earnings');

        $operationalCost = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $totalCost = $payrollCost + $operationalCost;
        $profit = $revenue - $totalCost;
        $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

        $prevMonth = $now->copy()->subMonth();
        $prevRevenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prevMonth->month)
            ->whereYear('paid_at', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $prevPayroll = EmployeePayroll::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prevMonth->month)
            ->whereYear('paid_at', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('total_earnings');

        $prevOperational = BranchMonthlyCost::where('period_month', $prevMonth->month)
            ->where('period_year', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $prevProfit = $prevRevenue - ($prevPayroll + $prevOperational);
        $trend = $prevProfit != 0 ? round((($profit - $prevProfit) / abs($prevProfit)) * 100, 1) : 0;

        return [
            'revenue' => $revenue,
            'total_cost' => $totalCost,
            'payroll_cost' => $payrollCost,
            'operational_cost' => $operationalCost,
            'profit' => $profit,
            'margin_percent' => $margin,
            'trend_percent' => $trend,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $monthlyData = [];
        for ($m = 1; $m <= $now->month; $m++) {
            $rev = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $m)
                ->whereYear('paid_at', $now->year)
                ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
                ->sum('amount');

            $cost = EmployeePayroll::where('payment_status', 'paid')
                ->whereMonth('paid_at', $m)
                ->whereYear('paid_at', $now->year)
                ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($r) => $r->where('branch_id', $branchId)))
                ->sum('total_earnings');

            $opCost = BranchMonthlyCost::where('period_month', $m)
                ->where('period_year', $now->year)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->sum('amount');

            $monthlyData[] = [
                'month' => Carbon::create($now->year, $m)->translatedFormat('F'),
                'revenue' => $rev,
                'payroll_cost' => $cost,
                'operational_cost' => $opCost,
                'total_cost' => $cost + $opCost,
                'profit' => $rev - ($cost + $opCost),
            ];
        }

        return $monthlyData;
    }
}
