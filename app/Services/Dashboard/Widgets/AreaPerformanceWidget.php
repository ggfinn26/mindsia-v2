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

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // If scoped to a branch, find its area
        $areaQuery = Area::query();
        if ($branchId) {
            $branch = Branch::find($branchId, ['areas_id']);
            if ($branch?->areas_id) {
                $areaQuery->where('id', $branch->areas_id);
            }
        }

        $areas = $areaQuery->with('branches:id,areas_id,branch_name')->get();

        $areaPerformance = $areas->map(function ($area) use ($currentMonth, $currentYear) {
            $branchIds = $area->branches->pluck('id');

            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration.memberData', fn ($r) => $r->whereIn('branch_id', $branchIds))
                ->sum('amount');

            $cost = BranchMonthlyCost::where('period_month', $currentMonth)
                ->where('period_year', $currentYear)
                ->whereIn('branch_id', $branchIds)
                ->sum('amount');

            $newMembers = MemberRegistration::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->whereHas('memberData', fn ($m) => $m->whereIn('branch_id', $branchIds))
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

    public function getExportData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $areas = Area::with('branches:id,areas_id')->get();

        return $areas->map(function ($area) use ($currentMonth, $currentYear) {
            $branchIds = $area->branches->pluck('id');
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration.memberData', fn ($r) => $r->whereIn('branch_id', $branchIds))
                ->sum('amount');

            return [
                'area' => $area->area_name,
                'revenue' => $revenue,
                'branch_count' => $branchIds->count(),
            ];
        })->toArray();
    }
}
