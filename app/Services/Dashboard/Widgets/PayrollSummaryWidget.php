<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class PayrollSummaryWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'payroll_summary';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $period = PayrollPeriod::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->first();

        $query = EmployeePayroll::whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)));

        $totalEarnings = (clone $query)->sum('total_earnings');
        $totalDeductions = (clone $query)->sum('total_deductions');
        $netAmount = (clone $query)->sum('net_amount');
        $employeeCount = (clone $query)->count();

        $byPaymentStatus = (clone $query)
            ->selectRaw('payment_status, COUNT(*) as count')
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status')
            ->toArray();

        $prevMonth = $now->copy()->subMonth();
        $prevNet = EmployeePayroll::whereMonth('paid_at', $prevMonth->month)
            ->whereYear('paid_at', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->sum('net_amount');

        $trend = $prevNet > 0
            ? round((($netAmount - $prevNet) / $prevNet) * 100, 1)
            : 0;

        return [
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_amount' => $netAmount,
            'employee_count' => $employeeCount,
            'by_payment_status' => $byPaymentStatus,
            'period_status' => $period?->status ?? 'draft',
            'trend_percent' => $trend,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $payrolls = EmployeePayroll::whereYear('paid_at', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->get();

        return $payrolls->map(fn ($p) => [
            'employee' => $p->employee_name_snapshot,
            'position' => $p->position_name_snapshot,
            'branch' => $p->branch_name_snapshot,
            'total_earnings' => $p->total_earnings,
            'total_deductions' => $p->total_deductions,
            'net_amount' => $p->net_amount,
            'payment_status' => $p->payment_status,
            'paid_at' => $p->paid_at?->format('Y-m-d'),
        ])->toArray();
    }
}
