<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Area;
use App\Models\Branch;
use App\Models\BranchMonthlyCost;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class AreaPerformanceWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'area_performance';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // If scoped to a branch, find its area
        $areaQuery = Area::query();
        if ($branchId) {
            $branch = Branch::find($branchId, ['area_id']);
            if ($branch?->area_id) {
                $areaQuery->where('id', $branch->area_id);
            }
        }

        $areas = $areaQuery->with('branches:id,area_id,branch_name')->get();

        $areaPerformance = $areas->map(function ($area) use ($currentMonth, $currentYear) {
            $branchIds = $area->branches->pluck('id');

            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration', fn ($r) => $r->whereIn('branch_id', $branchIds))
                ->sum('amount');

            $cost = BranchMonthlyCost::where('period_month', $currentMonth)
                ->where('period_year', $currentYear)
                ->whereIn('branch_id', $branchIds)
                ->sum('amount');

            $newMembers = MemberRegistration::whereMonth('registration_date', $currentMonth)
                ->whereYear('registration_date', $currentYear)
                ->whereIn('branch_id', $branchIds)
                ->count();

            return [
                'area' => $area->area_name,
                'branch_count' => $branchIds->count(),
                'revenue' => $revenue,
                'cost' => $cost,
                'margin' => $revenue - $cost,
                'new_members' => $newMembers,
            ];
        })->sortByDesc('revenue')->values()->toArray();

        return [
            'areas' => $areaPerformance,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $areas = Area::with('branches:id,area_id')->get();

        return $areas->map(function ($area) use ($currentMonth, $currentYear) {
            $branchIds = $area->branches->pluck('id');
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration', fn ($r) => $r->whereIn('branch_id', $branchIds))
                ->sum('amount');

            return [
                'area' => $area->area_name,
                'revenue' => $revenue,
                'branch_count' => $branchIds->count(),
            ];
        })->toArray();
    }
}
