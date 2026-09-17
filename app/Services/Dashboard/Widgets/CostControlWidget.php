<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchMonthlyCost;
use App\Models\BudgetEstimate;
use App\Models\EmployeePayroll;
use App\Models\Reimbursement;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class CostControlWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'cost_control';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $budgetTotal = BudgetEstimate::where('status', 'approved')
            ->where('period_year', $currentYear)
            ->where('period_month', $currentMonth)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        $operationalCost = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $payrollCost = EmployeePayroll::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('total_earnings');

        $reimbursementCost = Reimbursement::where('status', 'PAID')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        $actualTotal = $operationalCost + $payrollCost + $reimbursementCost;
        $variance = $budgetTotal - $actualTotal;
        $utilization = $budgetTotal > 0 ? round(($actualTotal / $budgetTotal) * 100, 1) : 0;

        $costByCategory = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $pendingBudgetCount = BudgetEstimate::where('status', 'draft')
            ->where('period_year', $currentYear)
            ->where('period_month', $currentMonth)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        return [
            'budget_total' => $budgetTotal,
            'actual_total' => $actualTotal,
            'operational_cost' => $operationalCost,
            'payroll_cost' => $payrollCost,
            'reimbursement_cost' => $reimbursementCost,
            'variance' => $variance,
            'utilization_percent' => $utilization,
            'cost_by_category' => $costByCategory,
            'pending_budget_count' => $pendingBudgetCount,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $budgets = BudgetEstimate::where('period_year', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('branch')
            ->orderBy('period_month')
            ->get();

        return $budgets->map(fn ($b) => [
            'branch' => $b->branch?->branch_name ?? '-',
            'period' => "{$b->period_year}-{$b->period_month}",
            'title' => $b->title,
            'budget_amount' => $b->total_amount,
            'status' => $b->status,
        ])->toArray();
    }
}
