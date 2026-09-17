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

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $evaluation = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->whereMonth('evaluation_period', $now->month)
            ->whereYear('evaluation_period', $now->year)
            ->with('items:id,evaluation_id,indicator_name,target_value,actual_value,weight,score')
            ->first();

        $totalScore = $evaluation?->total_score ?? 0;
        $grade = $evaluation?->grade ?? '-';

        $items = $evaluation?->items->map(fn ($item) => [
            'indicator' => $item->indicator_name,
            'target' => $item->target_value,
            'actual' => $item->actual_value,
            'weight' => $item->weight,
            'score' => $item->score,
        ])->toArray() ?? [];

        return [
            'total_score' => $totalScore,
            'grade' => $grade,
            'items' => $items,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        $evaluations = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->with('items')
            ->orderBy('evaluation_period', 'desc')
            ->get();

        return $evaluations->flatMap(fn ($ev) => $ev->items->map(fn ($item) => [
            'period' => $ev->evaluation_period?->format('Y-m'),
            'indicator' => $item->indicator_name,
            'target' => $item->target_value,
            'actual' => $item->actual_value,
            'score' => $item->score,
            'grade' => $ev->grade,
        ]))->toArray();
    }
}
