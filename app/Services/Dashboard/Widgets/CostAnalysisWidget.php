<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchMonthlyCost;
use App\Models\EmployeePayroll;
use App\Models\Reimbursement;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class CostAnalysisWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'cost_analysis';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $opex = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->selectRaw('cost_category, SUM(amount) as total')
            ->groupBy('cost_category')
            ->pluck('total', 'cost_category')
            ->toArray();

        $payroll = EmployeePayroll::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->sum('net_amount');

        $reimbursement = Reimbursement::where('status', 'approved')
            ->whereMonth('updated_at', $currentMonth)
            ->whereYear('updated_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->sum('total_amount');

        $breakdown = array_merge($opex, [
            'Payroll' => $payroll,
            'Reimbursement' => $reimbursement,
        ]);

        arsort($breakdown);

        $totalCost = array_sum($breakdown);

        return [
            'breakdown' => $breakdown,
            'total_cost' => $totalCost,
            'payroll_cost' => $payroll,
            'opex_total' => array_sum($opex),
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $costs = BranchMonthlyCost::where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('branch:id,branch_name')
            ->get();

        return $costs->map(fn ($c) => [
            'branch' => $c->branch?->branch_name ?? '-',
            'category' => $c->cost_category ?? '-',
            'amount' => $c->amount,
            'period' => $c->period_month.'/'.$c->period_year,
        ])->toArray();
    }
}
