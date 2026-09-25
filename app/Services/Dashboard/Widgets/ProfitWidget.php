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

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $scope = $filters['scope'] ?? 'cabang';
        $areaId = $filters['area_id'] ?? null;
        $currentMonth = (int) ($filters['month'] ?? now()->month);
        $currentYear = (int) ($filters['year'] ?? now()->year);
        $targetDate = Carbon::create($currentYear, $currentMonth, 1);
        $now = $targetDate->copy();

        // Override branchId if scope is not cabang
        if ($scope === 'nasional') {
            $branchId = null;
        }
        // For area, we would need to join branch.area_id, but for now we'll pass area_id dynamically

        $revenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($scope === 'cabang' && $branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('registration.branch', fn ($b) => $b->where('area_id', $areaId)))
            ->sum('amount');

        $payrollCost = EmployeePayroll::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($scope === 'cabang' && $branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('employee.branch', fn ($b) => $b->where('area_id', $areaId)))
            ->sum('net_amount');

        $operationalCost = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($scope === 'cabang' && $branchId, fn ($q) => $q->where('branch_id', $branchId))->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('branch', fn ($b) => $b->where('area_id', $areaId)))
            ->sum('amount');

        $totalCost = $payrollCost + $operationalCost;
        $profit = $revenue - $totalCost;
        $marginPercent = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

        $prevMonth = $now->copy()->subMonth();
        $prevRevenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prevMonth->month)
            ->whereYear('paid_at', $prevMonth->year)
            ->when($scope === 'cabang' && $branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('registration.branch', fn ($b) => $b->where('area_id', $areaId)))
            ->sum('amount');

        $prevPayrollCost = EmployeePayroll::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prevMonth->month)
            ->whereYear('paid_at', $prevMonth->year)
            ->when($scope === 'cabang' && $branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('employee.branch', fn ($b) => $b->where('area_id', $areaId)))
            ->sum('net_amount');

        $prevOperationalCost = BranchMonthlyCost::where('period_month', $prevMonth->month)
            ->where('period_year', $prevMonth->year)
            ->when($scope === 'cabang' && $branchId, fn ($q) => $q->where('branch_id', $branchId))->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('branch', fn ($b) => $b->where('area_id', $areaId)))
            ->sum('amount');

        $prevProfit = $prevRevenue - ($prevPayrollCost + $prevOperationalCost);
        $trend = $prevProfit != 0 ? round((($profit - $prevProfit) / abs($prevProfit)) * 100, 1) : 0;

        // Generate 6 months history
        $historyData = [];
        $historyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $mDate = $now->copy()->subMonths($i);

            $rev = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $mDate->month)->whereYear('paid_at', $mDate->year)
                ->when($scope === 'cabang' && $branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
                ->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('registration.branch', fn ($b) => $b->where('area_id', $areaId)))->sum('amount');

            $pay = EmployeePayroll::where('payment_status', 'paid')
                ->whereMonth('paid_at', $mDate->month)->whereYear('paid_at', $mDate->year)
                ->when($scope === 'cabang' && $branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
                ->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('employee.branch', fn ($b) => $b->where('area_id', $areaId)))->sum('net_amount');

            $op = BranchMonthlyCost::where('period_month', $mDate->month)->where('period_year', $mDate->year)
                ->when($scope === 'cabang' && $branchId, fn ($q) => $q->where('branch_id', $branchId))->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('branch', fn ($b) => $b->where('area_id', $areaId)))->sum('amount');

            $historyLabels[] = $mDate->format('M');
            $historyData[] = $rev - ($pay + $op);
        }

        return [
            'revenue' => $revenue,
            'payroll_cost' => $payrollCost,
            'operational_cost' => $operationalCost,
            'total_cost' => $totalCost,
            'profit' => $profit,
            'margin_percent' => $marginPercent,
            'trend_percent' => $trend,
            'period' => $now->translatedFormat('F Y'),
            'history_data' => $historyData,
            'history_labels' => $historyLabels,
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $scope = $filters['scope'] ?? 'cabang';
        $areaId = $filters['area_id'] ?? null;

        // Override branchId if scope is not cabang
        if ($scope === 'nasional') {
            $branchId = null;
        }

        $now = Carbon::now();
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $month = Carbon::create($now->year, $i, 1);
            if ($month->isFuture()) {
                continue;
            }

            $rev = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $i)->whereYear('paid_at', $now->year)
                ->when($scope === 'cabang' && $branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
                ->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('registration.branch', fn ($b) => $b->where('area_id', $areaId)))->sum('amount');

            $pay = EmployeePayroll::where('payment_status', 'paid')
                ->whereMonth('paid_at', $i)->whereYear('paid_at', $now->year)
                ->when($scope === 'cabang' && $branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
                ->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('employee.branch', fn ($b) => $b->where('area_id', $areaId)))->sum('net_amount');

            $op = BranchMonthlyCost::where('period_month', $i)->where('period_year', $now->year)
                ->when($scope === 'cabang' && $branchId, fn ($q) => $q->where('branch_id', $branchId))->when($scope === 'area' && $areaId, fn ($q) => $q->whereHas('branch', fn ($b) => $b->where('area_id', $areaId)))->sum('amount');

            $data[] = [
                'month' => $month->format('M Y'),
                'revenue' => $rev,
                'payroll' => $pay,
                'operational' => $op,
                'profit' => $rev - ($pay + $op),
            ];
        }

        return $data;
    }
}
