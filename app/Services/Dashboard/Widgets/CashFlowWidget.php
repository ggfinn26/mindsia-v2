<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchMonthlyCost;
use App\Models\EmployeePayroll;
use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class CashFlowWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'cash_flow';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $inflow = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $payrollOutflow = EmployeePayroll::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->sum('net_amount');

        $opexOutflow = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $totalOutflow = $payrollOutflow + $opexOutflow;
        $netCashFlow = $inflow - $totalOutflow;

        // Weekly breakdown for current month
        $weeklyInflow = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->selectRaw('WEEK(paid_at) as week, SUM(amount) as total')
            ->groupByRaw('WEEK(paid_at)')
            ->pluck('total', 'week')
            ->toArray();

        return [
            'inflow' => $inflow,
            'payroll_outflow' => $payrollOutflow,
            'opex_outflow' => $opexOutflow,
            'total_outflow' => $totalOutflow,
            'net_cash_flow' => $netCashFlow,
            'weekly_inflow' => $weeklyInflow,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $payments = MemberPayment::where('payment_status', 'paid')
            ->whereYear('paid_at', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->selectRaw('MONTH(paid_at) as month, SUM(amount) as inflow')
            ->groupByRaw('MONTH(paid_at)')
            ->pluck('inflow', 'month')
            ->toArray();

        return collect(range(1, $now->month))->map(fn ($m) => [
            'month' => $m,
            'inflow' => $payments[$m] ?? 0,
        ])->toArray();
    }
}
