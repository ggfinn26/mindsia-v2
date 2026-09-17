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

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        // Bonus/incentive calculated this month
        $bonusCalculation = PayrollBonusCalculation::where('employee_id', $employeeId)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->get();

        $pendingBonus = $bonusCalculation->where('payment_status', 'pending')->sum('bonus_amount');
        $paidBonus = $bonusCalculation->where('payment_status', 'paid')->sum('bonus_amount');

        // Commission via payroll items
        $commissionItems = PayrollItem::whereHas('payroll', fn ($q) => $q
            ->where('employee_id', $employeeId)
            ->whereMonth('paid_at', $now->month)
            ->whereYear('paid_at', $now->year))
            ->where('component_type', 'bonus')
            ->get();

        $commissionTotal = $commissionItems->sum('amount');

        // Forecast: last 3 months avg
        $avgBonus = PayrollBonusCalculation::where('employee_id', $employeeId)
            ->where('created_at', '>=', $now->copy()->subMonths(3))
            ->avg('bonus_amount') ?? 0;

        return [
            'pending_bonus' => $pendingBonus,
            'paid_bonus' => $paidBonus,
            'commission_total' => $commissionTotal,
            'forecast_next' => round($avgBonus),
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return PayrollBonusCalculation::where('employee_id', $employeeId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($b) => [
                'period' => $b->created_at?->format('Y-m'),
                'bonus_amount' => $b->bonus_amount,
                'payment_status' => $b->payment_status,
            ])->toArray();
    }
}
