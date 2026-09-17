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

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        // Last 3 months KPI evaluations
        $kpiHistory = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->where('evaluation_period', '>=', $now->copy()->subMonths(3)->startOfMonth())
            ->orderBy('evaluation_period')
            ->get()
            ->map(fn ($ev) => [
                'period' => $ev->evaluation_period?->translatedFormat('M Y'),
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
                'total_score' => $s->total_score,
                'grade' => $s->grade,
            ])->toArray();

        $history = count($kpiHistory) > 0 ? $kpiHistory : $marketingHistory;

        return [
            'history' => $history,
            'trend' => count($history) >= 2
                ? round($history[count($history) - 1]['total_score'] - $history[0]['total_score'], 2)
                : 0,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->orderBy('evaluation_period', 'desc')
            ->get()
            ->map(fn ($ev) => [
                'period' => $ev->evaluation_period?->format('Y-m'),
                'total_score' => $ev->total_score,
                'grade' => $ev->grade,
            ])->toArray();
    }
}
