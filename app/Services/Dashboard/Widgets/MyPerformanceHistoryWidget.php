<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeKpiEvaluation;
use App\Models\MarketingKpiSnapshot;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyPerformanceHistoryWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_performance_history';
    }

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        // Last 3 months KPI evaluations
        $kpiHistory = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->where('period_start_date', '>=', $now->copy()->subMonths(3)->startOfMonth())
            ->orderBy('period_start_date')
            ->get()
            ->map(fn ($ev) => [
                'period' => $ev->period_start_date?->translatedFormat('M Y'),
                'total_score' => $ev->total_score,
                'grade' => $ev->grade,
            ])->toArray();

        // Last 3 months marketing KPI snapshot (if applicable)
        $marketingHistory = MarketingKpiSnapshot::where('employee_id', $employeeId)
            ->where('created_at', '>=', $now->copy()->subMonths(3))
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->get()
            ->map(fn ($s) => [
                'period' => $s->period_month.'/'.$s->period_year,
                'score' => (float) $s->mpi_score,
            ])->toArray();

        $history = count($kpiHistory) > 0 ? $kpiHistory : $marketingHistory;
        $scoreKey = count($kpiHistory) > 0 ? 'total_score' : 'score';

        return [
            'history' => $history,
            'trend' => count($history) >= 2
                ? round(($history[count($history) - 1][$scoreKey] ?? 0) - ($history[0][$scoreKey] ?? 0), 2)
                : 0,
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

        return EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->orderBy('period_start_date', 'desc')
            ->get()
            ->map(fn ($ev) => [
                'period' => $ev->period_start_date?->format('Y-m'),
                'total_score' => $ev->total_score,
                'grade' => $ev->grade,
            ])->toArray();
    }
}
