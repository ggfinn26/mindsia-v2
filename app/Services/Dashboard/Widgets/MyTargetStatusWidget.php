<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MarketingPerformance;
use App\Models\MarketingTarget;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyTargetStatusWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_target_status';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $target = MarketingTarget::where('employee_id', $employeeId)
            ->where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->first();

        $performance = MarketingPerformance::where('employee_id', $employeeId)
            ->where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->first();

        $targetMember = $target?->target_member ?? 0;
        $actualMember = $performance?->total_member ?? 0;
        $targetOmzet = $target?->target_omzet ?? 0;
        $actualOmzet = $performance?->total_revenue ?? 0;

        return [
            'target_member' => $targetMember,
            'actual_member' => $actualMember,
            'member_achievement' => $targetMember > 0
                ? round(($actualMember / $targetMember) * 100, 1)
                : null,
            'target_omzet' => $targetOmzet,
            'actual_omzet' => $actualOmzet,
            'omzet_achievement' => $targetOmzet > 0
                ? round(($actualOmzet / $targetOmzet) * 100, 1)
                : null,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        $targets = MarketingTarget::where('employee_id', $employeeId)
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->get();

        $performances = MarketingPerformance::where('employee_id', $employeeId)
            ->get()
            ->keyBy(fn ($p) => $p->period_year.'-'.$p->period_month);

        return $targets->map(function ($t) use ($performances) {
            $perf = $performances->get($t->period_year.'-'.$t->period_month);

            return [
                'period' => $t->period_month.'/'.$t->period_year,
                'target_member' => $t->target_member ?? 0,
                'actual_member' => $perf?->total_member ?? 0,
            ];
        })->toArray();
    }
}
