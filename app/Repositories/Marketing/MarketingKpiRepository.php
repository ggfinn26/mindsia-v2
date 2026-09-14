<?php

namespace App\Repositories\Marketing;

use App\Models\MarketingKpiRankingRule;
use App\Models\MarketingKpiSnapshot;
use Illuminate\Database\Eloquent\Collection;

class MarketingKpiRepository
{
    public function latestByPeriod(int $month, int $year): Collection
    {
        // latest snapshot per employee, sorted: rank_national → classes_pct → reg_value_pct → name
        return MarketingKpiSnapshot::with('employee.branch')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->whereIn('id', function ($sub) use ($month, $year) {
                $sub->selectRaw('MAX(id)')
                    ->from('marketing_kpi_snapshots')
                    ->where('period_month', $month)
                    ->where('period_year', $year)
                    ->groupBy('employee_id');
            })
            ->orderBy('rank_national')
            ->orderByDesc('classes_percentage')
            ->orderByDesc('registration_value_percentage')
            ->orderBy('employee_name_snapshot')
            ->get();
    }

    public function historyByEmployee(int $employeeId, int $month, int $year): Collection
    {
        return MarketingKpiSnapshot::where('employee_id', $employeeId)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('id')
            ->get();
    }

    public function append(array $data, array $criteriaRows): MarketingKpiSnapshot
    {
        $snapshot = MarketingKpiSnapshot::create($data);

        if (! empty($criteriaRows)) {
            $snapshot->snapshotCriteria()->createMany($criteriaRows);
        }

        return $snapshot;
    }

    public function finalizePreviousPeriod(int $month, int $year): void
    {
        MarketingKpiSnapshot::where('period_month', $month)
            ->where('period_year', $year)
            ->whereNull('finalized_at')
            ->whereIn('id', function ($sub) use ($month, $year) {
                $sub->selectRaw('MAX(id)')
                    ->from('marketing_kpi_snapshots')
                    ->where('period_month', $month)
                    ->where('period_year', $year)
                    ->groupBy('employee_id');
            })
            ->update(['finalized_at' => now()]);
    }

    public function activeRankingRule(): MarketingKpiRankingRule
    {
        return MarketingKpiRankingRule::where('is_active', true)
            ->with('criteria.automaticCriterion', 'criteria.manualCriterion')
            ->firstOrFail();
    }
}
