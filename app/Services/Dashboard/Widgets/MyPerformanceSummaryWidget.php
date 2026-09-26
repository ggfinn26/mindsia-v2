<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeKpiEvaluation;
use App\Models\MarketingKpiSnapshot;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyPerformanceSummaryWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_performance_summary';
    }

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        // Try KPI evaluation first, fall back to marketing snapshot
        $kpiEval = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->whereMonth('period_start_date', $now->month)
            ->whereYear('period_start_date', $now->year)
            ->first();

        if ($kpiEval) {
            return [
                'score' => $kpiEval->total_score,
                'grade' => $kpiEval->grade,
                'source' => 'kpi_evaluation',
                'period' => $now->translatedFormat('F Y'),
            ];
        }

        $snapshot = MarketingKpiSnapshot::where('employee_id', $employeeId)
            ->where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->first();

        return [
            'score' => $snapshot?->mpi_score ?? null,
            'grade' => $snapshot?->grade ?? null,
            'source' => 'marketing_snapshot',
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        $kpiHistory = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->orderBy('period_start_date', 'desc')
            ->get()
            ->map(fn ($ev) => [
                'period' => $ev->period_start_date?->format('Y-m'),
                'score' => $ev->total_score,
                'grade' => $ev->grade,
            ])->toArray();

        return count($kpiHistory) > 0 ? $kpiHistory
            : MarketingKpiSnapshot::where('employee_id', $employeeId)
                ->orderBy('period_year', 'desc')
                ->orderBy('period_month', 'desc')
                ->get()
                ->map(fn ($s) => [
                    'period' => $s->period_year.'-'.str_pad($s->period_month, 2, '0', STR_PAD_LEFT),
                    'score' => $s->mpi_score,
                    'grade' => $s->grade,
                ])->toArray();
    }
}
