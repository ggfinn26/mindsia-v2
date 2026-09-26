<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\PayrollBonusCalculation;
use App\Models\PayrollItem;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyCommissionIncentiveWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_commission_incentive';
    }

    public function getData(array $filters = []): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        // Bonus items from payroll this month
        $bonusItems = PayrollItem::whereHas('employeePayroll', fn ($q) => $q
            ->where('employee_id', $employeeId)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year))
            ->where('component_type_snapshot', 'bonus')
            ->get();

        $totalBonus = $bonusItems->sum('total_amount');

        // Bonus calculations this month
        $bonusCalcs = PayrollBonusCalculation::whereHas('employeePayroll', fn ($q) => $q
            ->where('employee_id', $employeeId)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year))
            ->get();

        $totalCalcBonus = $bonusCalcs->sum('calculated_amount');

        // 3-month avg forecast
        $avgBonus = PayrollItem::whereHas('employeePayroll', fn ($q) => $q
            ->where('employee_id', $employeeId)
            ->where('created_at', '>=', $now->copy()->subMonths(3)))
            ->where('component_type_snapshot', 'bonus')
            ->avg('total_amount') ?? 0;

        return [
            'bonus_items' => $bonusItems->count(),
            'total_bonus' => $totalBonus,
            'total_calc_bonus' => $totalCalcBonus,
            'forecast_next' => round($avgBonus),
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return PayrollItem::whereHas('employeePayroll', fn ($q) => $q->where('employee_id', $employeeId))
            ->where('component_type_snapshot', 'bonus')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($b) => [
                'period' => $b->created_at?->format('Y-m'),
                'component' => $b->component_name_snapshot,
                'amount' => $b->total_amount,
            ])->toArray();
    }
}
