<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchMonthlyCost;
use App\Models\Reimbursement;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class ExpenseTrackingWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'expense_tracking';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $opexByCategory = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->selectRaw('cost_category, SUM(amount) as total')
            ->groupBy('cost_category')
            ->orderByRaw('total DESC')
            ->pluck('total', 'cost_category')
            ->toArray();

        $reimbursementTotal = Reimbursement::whereIn('status', ['paid', 'approved'])
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        $totalOpex = array_sum($opexByCategory);

        $prevMonth = $now->copy()->subMonth();
        $prevOpex = BranchMonthlyCost::where('period_month', $prevMonth->month)
            ->where('period_year', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $trend = $prevOpex > 0
            ? round((($totalOpex - $prevOpex) / $prevOpex) * 100, 1)
            : 0;

        return [
            'by_category' => $opexByCategory,
            'total_opex' => $totalOpex,
            'reimbursement_total' => $reimbursementTotal,
            'trend_percent' => $trend,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $costs = BranchMonthlyCost::whereYear('period_year', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('branch:id,branch_name')
            ->orderBy('period_month')
            ->get();

        return $costs->map(fn ($c) => [
            'branch' => $c->branch?->branch_name ?? '-',
            'category' => $c->cost_category ?? '-',
            'amount' => $c->amount,
            'period' => $c->period_month.'/'.$c->period_year,
        ])->toArray();
    }
}
