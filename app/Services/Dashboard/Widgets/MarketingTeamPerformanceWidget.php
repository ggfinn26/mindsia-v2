<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MarketingPerformance;
use App\Models\MarketingTarget;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MarketingTeamPerformanceWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'marketing_team_performance';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $performances = MarketingPerformance::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('employee:id,full_name')
            ->get();

        $targets = MarketingTarget::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get()
            ->keyBy('employee_id');

        $ranking = $performances->map(function ($mp) use ($targets) {
            $target = $targets->get($mp->employee_id);
            $achievement = $target && $target->target_member > 0
                ? round(($mp->total_member / $target->target_member) * 100, 1)
                : null;

            return [
                'employee' => $mp->employee?->full_name ?? '-',
                'total_member' => $mp->total_member ?? 0,
                'target_member' => $target?->target_member ?? 0,
                'achievement_percent' => $achievement,
            ];
        })->sortByDesc('total_member')->values()->toArray();

        $teamTotal = $performances->sum('total_member');
        $teamTarget = $targets->sum('target_member');
        $teamAchievement = $teamTarget > 0
            ? round(($teamTotal / $teamTarget) * 100, 1)
            : 0;

        return [
            'ranking' => $ranking,
            'team_total_member' => $teamTotal,
            'team_target_member' => $teamTarget,
            'team_achievement_percent' => $teamAchievement,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $performances = MarketingPerformance::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('employee:id,full_name')
            ->get();

        $targets = MarketingTarget::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get()
            ->keyBy('employee_id');

        return $performances->map(function ($mp) use ($targets) {
            $target = $targets->get($mp->employee_id);

            return [
                'employee' => $mp->employee?->full_name ?? '-',
                'total_member' => $mp->total_member ?? 0,
                'target_member' => $target?->target_member ?? 0,
                'total_revenue' => $mp->total_revenue ?? 0,
            ];
        })->sortByDesc('total_member')->values()->toArray();
    }
}
