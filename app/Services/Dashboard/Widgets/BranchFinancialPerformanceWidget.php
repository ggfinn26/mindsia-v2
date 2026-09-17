<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Branch;
use App\Models\BranchMonthlyCost;
use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class BranchFinancialPerformanceWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'branch_financial_performance';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $branches = Branch::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('id', $branchId))
            ->get(['id', 'branch_name']);

        $branchPerformance = $branches->map(function ($branch) use ($currentMonth, $currentYear) {
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration', fn ($r) => $r->where('branch_id', $branch->id))
                ->sum('amount');

            $cost = BranchMonthlyCost::where('period_month', $currentMonth)
                ->where('period_year', $currentYear)
                ->where('branch_id', $branch->id)
                ->sum('amount');

            $margin = $revenue - $cost;
            $marginPercent = $revenue > 0 ? round(($margin / $revenue) * 100, 1) : 0;

            return [
                'branch' => $branch->branch_name,
                'revenue' => $revenue,
                'cost' => $cost,
                'margin' => $margin,
                'margin_percent' => $marginPercent,
            ];
        })->sortByDesc('revenue')->values()->toArray();

        return [
            'branches' => $branchPerformance,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $branches = Branch::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('id', $branchId))
            ->get(['id', 'branch_name']);

        return $branches->map(function ($branch) use ($currentMonth, $currentYear) {
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration', fn ($r) => $r->where('branch_id', $branch->id))
                ->sum('amount');

            $cost = BranchMonthlyCost::where('period_month', $currentMonth)
                ->where('period_year', $currentYear)
                ->where('branch_id', $branch->id)
                ->sum('amount');

            return [
                'branch' => $branch->branch_name,
                'revenue' => $revenue,
                'cost' => $cost,
                'margin' => $revenue - $cost,
            ];
        })->toArray();
    }
}
