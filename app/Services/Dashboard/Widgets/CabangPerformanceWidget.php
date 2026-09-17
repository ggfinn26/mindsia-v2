<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchMonthlyCost;
use App\Models\Employee;
use App\Models\MemberClass;
use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class CabangPerformanceWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'cabang_performance';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $activeEmployees = Employee::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $revenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $operationalCost = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $activeClasses = MemberClass::where('status', 'active')
            ->when($branchId, fn ($q) => $q->whereHas('classRoom', fn ($c) => $c->where('branch_id', $branchId)))
            ->count();

        $prevMonth = $now->copy()->subMonth();
        $prevRevenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prevMonth->month)
            ->whereYear('paid_at', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $trend = $prevRevenue > 0
            ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : 0;

        return [
            'active_employees' => $activeEmployees,
            'revenue' => $revenue,
            'operational_cost' => $operationalCost,
            'active_classes' => $activeClasses,
            'trend_percent' => $trend,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $payments = MemberPayment::where('payment_status', 'paid')
            ->whereYear('paid_at', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->selectRaw('MONTH(paid_at) as month, SUM(amount) as total')
            ->groupByRaw('MONTH(paid_at)')
            ->orderBy('month')
            ->get();

        return $payments->map(fn ($p) => [
            'month' => $p->month,
            'revenue' => $p->total,
        ])->toArray();
    }
}
