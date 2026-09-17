<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Branch;
use App\Models\BranchMonthlyCost;
use App\Models\Employee;
use App\Models\MemberClass;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class BranchPerformanceWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'branch_performance';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $revenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $cost = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $newMembers = MemberRegistration::whereMonth('registration_date', $currentMonth)
            ->whereYear('registration_date', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $activeClasses = MemberClass::where('status', 'active')
            ->when($branchId, fn ($q) => $q->whereHas('classRoom', fn ($c) => $c->where('branch_id', $branchId)))
            ->count();

        $activeStaff = Employee::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $margin = $revenue - $cost;
        $marginPercent = $revenue > 0 ? round(($margin / $revenue) * 100, 1) : 0;

        $prevMonth = $now->copy()->subMonth();
        $prevRevenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prevMonth->month)
            ->whereYear('paid_at', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $trend = $prevRevenue > 0
            ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : 0;

        $branchName = $branchId
            ? Branch::find($branchId)?->branch_name ?? '-'
            : 'All Branches';

        return [
            'branch_name' => $branchName,
            'revenue' => $revenue,
            'cost' => $cost,
            'margin' => $margin,
            'margin_percent' => $marginPercent,
            'new_members' => $newMembers,
            'active_classes' => $activeClasses,
            'active_staff' => $activeStaff,
            'trend_percent' => $trend,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        return collect(range(1, $now->month))->map(function ($m) use ($now, $branchId) {
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $m)
                ->whereYear('paid_at', $now->year)
                ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
                ->sum('amount');

            $cost = BranchMonthlyCost::where('period_month', $m)
                ->where('period_year', $now->year)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->sum('amount');

            return [
                'month' => $m,
                'revenue' => $revenue,
                'cost' => $cost,
                'margin' => $revenue - $cost,
            ];
        })->toArray();
    }
}
