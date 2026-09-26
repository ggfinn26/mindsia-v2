<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MarketingKpiSnapshot;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyMpiProgressWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_mpi_progress';
    }

    public function getData(array $filters = []): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $now = Carbon::now();

        $current = MarketingKpiSnapshot::where('employee_id', $employeeId)
            ->where('period_year', $now->year)
            ->where('period_month', $now->month)
            ->with('snapshotCriteria')
            ->first();

        $history = MarketingKpiSnapshot::where('employee_id', $employeeId)
            ->where(function ($q) use ($now) {
                $q->where('period_year', '<', $now->year)
                    ->orWhere(function ($q2) use ($now) {
                        $q2->where('period_year', $now->year)
                            ->where('period_month', '<=', $now->month);
                    });
            })
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->limit(6)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($s) => [
                'period' => $s->period_month.'/'.$s->period_year,
                'mpi_score' => (float) $s->mpi_score,
            ])->toArray();

        $criteria = $current?->snapshotCriteria
            ->map(fn ($c) => [
                'name' => $c->criterion_name_snapshot,
                'value' => (float) $c->criterion_value,
            ])->toArray() ?? [];

        return [
            'mpi_score' => $current ? (float) $current->mpi_score : null,
            'rank_area' => $current?->rank_area,
            'rank_region' => $current?->rank_region,
            'rank_national' => $current?->rank_national,
            'metrics' => [
                [
                    'label' => 'Kelas',
                    'target' => $current?->classes_target,
                    'actual' => $current?->classes_actual,
                    'pct' => $current ? (float) $current->classes_percentage : null,
                    'icon' => 'school',
                ],
                [
                    'label' => 'Omzet',
                    'target' => $current?->omzet_target,
                    'actual' => $current?->registration_value_actual,
                    'pct' => $current ? (float) $current->registration_value_percentage : null,
                    'icon' => 'payments',
                ],
                [
                    'label' => 'Cash',
                    'target' => null,
                    'actual' => $current?->cash_collected_actual,
                    'pct' => $current ? (float) $current->cash_collected_percentage : null,
                    'icon' => 'account_balance_wallet',
                ],
                [
                    'label' => 'Prospek',
                    'target' => null,
                    'actual' => $current?->prospective_members_count,
                    'pct' => null,
                    'icon' => 'person_search',
                ],
                [
                    'label' => 'Member',
                    'target' => null,
                    'actual' => $current?->fixed_members_count,
                    'pct' => null,
                    'icon' => 'how_to_reg',
                ],
            ],
            'criteria' => $criteria,
            'history' => $history,
            'period' => $current
                ? $current->period_month.'/'.$current->period_year
                : $now->format('n/Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return MarketingKpiSnapshot::where('employee_id', $employeeId)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->get()
            ->map(fn ($s) => [
                'period' => $s->period_month.'/'.$s->period_year,
                'mpi_score' => $s->mpi_score,
                'rank_area' => $s->rank_area,
                'rank_region' => $s->rank_region,
                'rank_national' => $s->rank_national,
                'classes_pct' => $s->classes_percentage,
                'omzet_pct' => $s->registration_value_percentage,
                'cash_pct' => $s->cash_collected_percentage,
            ])->toArray();
    }
}
