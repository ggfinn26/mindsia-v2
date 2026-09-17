<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberRegistration;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MemberGrowthWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'member_growth';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        // Monthly trend for current year
        $monthlyGrowth = collect(range(1, $now->month))->map(fn ($m) => [
            'month' => $m,
            'count' => MemberRegistration::whereMonth('registration_date', $m)
                ->whereYear('registration_date', $now->year)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->count(),
        ])->toArray();

        // YoY comparison
        $ytdThis = MemberRegistration::whereYear('registration_date', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $ytdLast = MemberRegistration::whereYear('registration_date', $now->year - 1)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $yoyGrowth = $ytdLast > 0
            ? round((($ytdThis - $ytdLast) / $ytdLast) * 100, 1)
            : 0;

        $thisMonth = MemberRegistration::whereMonth('registration_date', $now->month)
            ->whereYear('registration_date', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        return [
            'this_month' => $thisMonth,
            'ytd_count' => $ytdThis,
            'ytd_last_year' => $ytdLast,
            'yoy_growth_percent' => $yoyGrowth,
            'monthly_trend' => $monthlyGrowth,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        return collect(range(1, $now->month))->map(fn ($m) => [
            'month' => $m,
            'year' => $now->year,
            'new_members' => MemberRegistration::whereMonth('registration_date', $m)
                ->whereYear('registration_date', $now->year)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->count(),
        ])->toArray();
    }
}
