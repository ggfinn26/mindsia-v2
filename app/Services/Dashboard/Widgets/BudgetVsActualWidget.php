<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchMonthlyCost;
use App\Models\BudgetEstimate;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class BudgetVsActualWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'budget_vs_actual';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $budget = BudgetEstimate::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        $actual = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $variance = $budget - $actual;
        $variancePercent = $budget > 0
            ? round(($variance / $budget) * 100, 1)
            : 0;

        $prevMonth = $now->copy()->subMonth();
        $prevBudget = BudgetEstimate::where('period_month', $prevMonth->month)
            ->where('period_year', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        $prevActual = BranchMonthlyCost::where('period_month', $prevMonth->month)
            ->where('period_year', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        return [
            'budget' => $budget,
            'actual' => $actual,
            'variance' => $variance,
            'variance_percent' => $variancePercent,
            'prev_budget' => $prevBudget,
            'prev_actual' => $prevActual,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $estimates = BudgetEstimate::whereYear('period_year', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('branch:id,branch_name')
            ->orderBy('period_month')
            ->get();

        return $estimates->map(fn ($b) => [
            'branch' => $b->branch?->branch_name ?? '-',
            'period' => $b->period_month.'/'.$b->period_year,
            'budget' => $b->total_amount,
            'status' => $b->status,
        ])->toArray();
    }
}
