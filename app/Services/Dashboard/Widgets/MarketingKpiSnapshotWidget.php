<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MarketingKpiSnapshot;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MarketingKpiSnapshotWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'marketing_kpi_snapshot';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $snapshots = MarketingKpiSnapshot::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('employee:id,full_name')
            ->orderBy('total_score', 'desc')
            ->get();

        $byGrade = $snapshots->groupBy('grade')
            ->map(fn ($g) => $g->count())
            ->toArray();

        $avgScore = $snapshots->avg('total_score') ?? 0;

        $ranking = $snapshots->take(10)->map(fn ($s) => [
            'employee' => $s->employee?->full_name ?? '-',
            'total_score' => $s->total_score,
            'grade' => $s->grade,
        ])->toArray();

        return [
            'total_employees_evaluated' => $snapshots->count(),
            'avg_score' => round($avgScore, 2),
            'by_grade' => $byGrade,
            'top_ranking' => $ranking,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $snapshots = MarketingKpiSnapshot::where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('employee:id,full_name', 'branch:id,branch_name')
            ->orderBy('total_score', 'desc')
            ->get();

        return $snapshots->map(fn ($s) => [
            'employee' => $s->employee?->full_name ?? '-',
            'branch' => $s->branch?->branch_name ?? '-',
            'total_score' => $s->total_score,
            'grade' => $s->grade,
        ])->toArray();
    }
}
