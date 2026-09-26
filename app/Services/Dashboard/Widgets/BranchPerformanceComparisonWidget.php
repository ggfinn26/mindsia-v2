<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Branch;
use App\Models\BranchMonthlyCost;
use App\Models\Employee;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class BranchPerformanceComparisonWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'branch_performance_comparison';
    }

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // If scoped to a branch, get branches in the same area
        $branchQuery = Branch::where('is_active', true);
        if ($branchId) {
            $area = Branch::find($branchId, ['areas_id']);
            if ($area?->areas_id) {
                $branchQuery->where('areas_id', $area->areas_id);
            }
        }

        $branches = $branchQuery->get(['id', 'branch_name']);

        $comparison = $branches->map(function ($branch) use ($currentMonth, $currentYear) {
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration.memberData', fn ($r) => $r->where('branch_id', $branch->id))
                ->sum('amount');

            $cost = BranchMonthlyCost::where('period_month', $currentMonth)
                ->where('period_year', $currentYear)
                ->where('branch_id', $branch->id)
                ->sum('amount');

            $newMembers = MemberRegistration::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->whereHas('memberData', fn ($m) => $m->where('branch_id', $branch->id))
                ->count();

            $activeStaff = Employee::where('is_active', true)
                ->where('branch_id', $branch->id)
                ->count();

            return [
                'branch' => $branch->branch_name,
                'revenue' => $revenue,
                'cost' => $cost,
                'margin' => $revenue - $cost,
                'new_members' => $newMembers,
                'active_staff' => $activeStaff,
            ];
        })->sortByDesc('revenue')->values()->toArray();

        return [
            'comparison' => $comparison,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();

        $branches = Branch::where('is_active', true)
            ->when($branchId, function ($q) use ($branchId) {
                $area = Branch::find($branchId, ['areas_id']);
                if ($area?->areas_id) {
                    $q->where('areas_id', $area->areas_id);
                }
            })
            ->get(['id', 'branch_name']);

        return $branches->map(function ($branch) use ($now) {
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $now->month)
                ->whereYear('paid_at', $now->year)
                ->whereHas('registration.memberData', fn ($r) => $r->where('branch_id', $branch->id))
                ->sum('amount');

            return [
                'branch' => $branch->branch_name,
                'revenue' => $revenue,
            ];
        })->toArray();
    }
}
