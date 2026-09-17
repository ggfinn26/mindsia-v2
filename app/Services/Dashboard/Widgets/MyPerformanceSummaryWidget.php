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

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        // Try KPI evaluation first, fall back to marketing snapshot
        $kpiEval = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->whereMonth('evaluation_period', $now->month)
            ->whereYear('evaluation_period', $now->year)
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
            'score' => $snapshot?->total_score ?? null,
            'grade' => $snapshot?->grade ?? null,
            'source' => 'marketing_snapshot',
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        $kpiHistory = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->orderBy('evaluation_period', 'desc')
            ->get()
            ->map(fn ($ev) => [
                'period' => $ev->evaluation_period?->format('Y-m'),
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
                    'score' => $s->total_score,
                    'grade' => $s->grade,
                ])->toArray();
    }
}
