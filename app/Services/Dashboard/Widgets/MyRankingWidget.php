<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MarketingKpiSnapshot;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyRankingWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_ranking';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $mySnapshot = MarketingKpiSnapshot::where('employee_id', $employeeId)
            ->where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->first();

        if (! $mySnapshot) {
            return [
                'rank_branch' => null,
                'rank_area' => null,
                'rank_national' => null,
                'total_score' => null,
                'grade' => null,
                'period' => $now->translatedFormat('F Y'),
            ];
        }

        // Rank within branch
        $rankBranch = MarketingKpiSnapshot::where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->where('branch_id', $mySnapshot->branch_id)
            ->where('total_score', '>', $mySnapshot->total_score)
            ->count() + 1;

        // Rank within area
        $rankArea = MarketingKpiSnapshot::where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->where('area_id', $mySnapshot->area_id)
            ->where('total_score', '>', $mySnapshot->total_score)
            ->count() + 1;

        // Rank nationally
        $rankNational = MarketingKpiSnapshot::where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->where('total_score', '>', $mySnapshot->total_score)
            ->count() + 1;

        return [
            'rank_branch' => $rankBranch,
            'rank_area' => $rankArea,
            'rank_national' => $rankNational,
            'total_score' => $mySnapshot->total_score,
            'grade' => $mySnapshot->grade,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return MarketingKpiSnapshot::where('employee_id', $employeeId)
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->get()
            ->map(fn ($s) => [
                'period' => $s->period_month.'/'.$s->period_year,
                'total_score' => $s->total_score,
                'grade' => $s->grade,
            ])->toArray();
    }
}
