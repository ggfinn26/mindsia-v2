<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeKpiEvaluation;
use App\Models\EmployeeSocialization;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class EmployeeDevelopmentWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'employee_development';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $kpiQuery = EmployeeKpiEvaluation::where('status', 'finalized')
            ->whereMonth('finalized_at', $currentMonth)
            ->whereYear('finalized_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)));

        $kpiCount = (clone $kpiQuery)->count();
        $avgScore = (clone $kpiQuery)->avg('total_score') ?? 0;

        $byGrade = (clone $kpiQuery)
            ->selectRaw('grade, COUNT(*) as count')
            ->groupBy('grade')
            ->pluck('count', 'grade')
            ->toArray();

        $socializationQuery = EmployeeSocialization::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)));

        $socializationCount = (clone $socializationQuery)->count();

        $bySocializationStatus = (clone $socializationQuery)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'kpi_evaluations' => $kpiCount,
            'avg_kpi_score' => round($avgScore, 1),
            'by_grade' => $byGrade,
            'socialization_count' => $socializationCount,
            'by_socialization_status' => $bySocializationStatus,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $evaluations = EmployeeKpiEvaluation::where('status', 'finalized')
            ->whereYear('finalized_at', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->get();

        return $evaluations->map(fn ($e) => [
            'employee' => $e->employee_name_snapshot,
            'position' => $e->position_name_snapshot,
            'period' => $e->period_start_date?->format('Y-m-d').' - '.$e->period_end_date?->format('Y-m-d'),
            'total_score' => $e->total_score,
            'grade' => $e->grade,
            'finalized_at' => $e->finalized_at?->format('Y-m-d'),
        ])->toArray();
    }
}
