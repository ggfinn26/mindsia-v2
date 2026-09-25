<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeePayroll;
use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class RevenueWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'revenue';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $memberRevenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $payrollCost = EmployeePayroll::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee.employmentStatus', fn ($r) => $r->where('position_id', '>', 0)))
            ->sum('net_amount');

        $prevMonth = $now->copy()->subMonth();
        $prevMemberRevenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prevMonth->month)
            ->whereYear('paid_at', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $trend = $prevMemberRevenue > 0
            ? round((($memberRevenue - $prevMemberRevenue) / $prevMemberRevenue) * 100, 1)
            : 0;

        // Generate 6 months history for sparkline chart
        $historyData = [];
        $historyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = $now->copy()->subMonths($i);

            $rev = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $monthDate->month)
                ->whereYear('paid_at', $monthDate->year)
                ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
                ->sum('amount');

            $historyLabels[] = $monthDate->format('M');
            $historyData[] = $rev;
        }

        return [
            'member_revenue' => $memberRevenue,
            'payroll_cost' => $payrollCost,
            'total_revenue' => $memberRevenue,
            'trend_percent' => $trend,
            'period' => $now->translatedFormat('F Y'),
            'history_data' => $historyData,
            'history_labels' => $historyLabels,
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $payments = MemberPayment::where('payment_status', 'paid')
            ->whereYear('paid_at', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->orderBy('paid_at')
            ->get();

        return $payments->map(fn ($p) => [
            'installment' => $p->installment_number,
            'amount' => $p->amount,
            'method' => $p->payment_method,
            'paid_at' => $p->paid_at?->format('Y-m-d'),
            'status' => $p->payment_status,
        ])->toArray();
    }
}
