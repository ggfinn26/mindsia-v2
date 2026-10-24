<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeKpiEvaluation;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyKpiAchievementWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_kpi_achievement';
    }

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $evaluation = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->whereMonth('period_start_date', $now->month)
            ->whereYear('period_start_date', $now->year)
            ->with('items:id,employee_kpi_evaluation_id,indicator_name_snapshot,target_value,actual_value,weight_snapshot,score')
            ->first();

        $totalScore = $evaluation?->total_score ?? 0;
        $grade = $evaluation?->grade ?? '-';

        $items = $evaluation?->items->map(fn ($item) => [
            'indicator' => $item->indicator_name_snapshot,
            'target' => $item->target_value,
            'actual' => $item->actual_value,
            'weight' => $item->weight_snapshot,
            'score' => $item->score,
        ])->toArray() ?? [];

        return [
            'total_score' => $totalScore,
            'grade' => $grade,
            'items' => $items,
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

        $evaluations = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->with('items')
            ->orderBy('period_start_date', 'desc')
            ->get();

        return $evaluations->flatMap(fn ($ev) => $ev->items->map(fn ($item) => [
            'period' => $ev->period_start_date?->format('Y-m'),
            'indicator' => $item->indicator_name_snapshot,
            'target' => $item->target_value,
            'actual' => $item->actual_value,
            'score' => $item->score,
            'grade' => $ev->grade,
        ]))->toArray();
    }
}
