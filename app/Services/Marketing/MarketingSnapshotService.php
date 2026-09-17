<?php

namespace App\Services\Marketing;

use App\Models\Employee;
use App\Models\MarketingKpiSnapshot;
use App\Models\MarketingPerformance;
use App\Models\MemberPayment;
use App\Repositories\Marketing\MarketingKpiRepository;
use App\Repositories\Marketing\MarketingTargetRepository;

class MarketingSnapshotService
{
    public function __construct(
        private readonly MarketingTargetRepository $targetRepository,
        private readonly MarketingKpiRepository $kpiRepository,
    ) {}

    public function append(Employee $employee, int $month, int $year): void
    {
        $rule = $this->kpiRepository->activeRankingRule();
        $target = $this->targetRepository->resolve($employee, $month, $year);

        $classesActual = $this->resolveClassesActual($employee->id, $month, $year);
        $cashCollected = $this->resolveCashCollected($employee->id, $month, $year);
        $adminFeeDeducted = $this->resolveAdminFeeDeducted($employee->id, $month, $year);
        $incomeActual = max(0.0, $cashCollected - $adminFeeDeducted);

        $classesPct = $target['classes_target'] > 0
            ? round(($classesActual / $target['classes_target']) * 100, 2)
            : 0.0;

        $cashPct = $target['omzet_target'] > 0
            ? round(($cashCollected / $target['omzet_target']) * 100, 2)
            : 0.0;

        // MPI = 50% × classes_pct + 50% × registration_value_pct
        $mpiScore = round(0.50 * $classesPct + 0.50 * $cashPct, 2);

        [$rankArea, $rankRegion, $rankNational] = $this->resolveRanking($employee, $month, $year, $mpiScore);

        $snapshotData = [
            'employee_id' => $employee->id,
            'ranking_rule_id' => $rule->id,
            'ranking_rule_code_snapshot' => $rule->rule_code,
            'ranking_rule_name_snapshot' => $rule->rule_name,
            'revenue_basis_snapshot' => $rule->revenue_basis,
            'employee_name_snapshot' => $employee->full_name,
            'branch_name_snapshot' => $employee->branch?->branch_name ?? '',
            'area_name_snapshot' => $employee->branch?->area?->area_name ?? '',
            'region_name_snapshot' => $employee->branch?->area?->region?->region_name ?? '',
            'period_month' => $month,
            'period_year' => $year,
            'classes_target' => $target['classes_target'],
            'classes_actual' => $classesActual,
            'classes_percentage' => $classesPct,
            'omzet_target' => $target['omzet_target'],
            'registration_value_actual' => $cashCollected,
            'registration_value_percentage' => $cashPct,
            'cash_collected_actual' => $cashCollected,
            'cash_collected_percentage' => $cashPct,
            'admin_fee_deducted' => $adminFeeDeducted,
            'income_actual' => $incomeActual,
            'mpi_score' => $mpiScore,
            'rank_area' => $rankArea,
            'rank_region' => $rankRegion,
            'rank_national' => $rankNational,
        ];

        $this->kpiRepository->append($snapshotData, []);
    }

    private function resolveClassesActual(int $employeeId, int $month, int $year): int
    {
        // classes_actual comes from marketing_performances which is updated by MarketingPerformanceObserver
        $performance = MarketingPerformance::where('employee_id', $employeeId)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->first();

        return $performance?->classes_actual ?? 0;
    }

    private function resolveCashCollected(int $employeeId, int $month, int $year): float
    {
        return (float) MemberPayment::whereHas(
            'registration',
            fn ($q) => $q->where('employee_id', $employeeId)
        )
            ->whereMonth('paid_at', $month)
            ->whereYear('paid_at', $year)
            ->where('payment_status', 'confirmed')
            ->sum('amount');
    }

    private function resolveAdminFeeDeducted(int $employeeId, int $month, int $year): float
    {
        $total = 0.0;

        // per_registration + on_registration: count registrations created in period
        $total += (float) \DB::table('member_registrations as mr')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->where('mr.employee_id', $employeeId)
            ->whereYear('mr.created_at', $year)
            ->whereMonth('mr.created_at', $month)
            ->where('p.admin_fee_mode', 'per_registration')
            ->where('p.admin_fee_timing', 'on_registration')
            ->sum('p.admin_fee');

        // per_registration + on_first_payment: count registrations whose first confirmed payment falls in period
        $total += (float) \DB::table('member_registrations as mr')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->joinSub(
                \DB::table('member_payments')
                    ->selectRaw('member_registration_id, MIN(paid_at) as first_paid')
                    ->where('payment_status', 'confirmed')
                    ->groupBy('member_registration_id'),
                'fp',
                'fp.member_registration_id', '=', 'mr.id'
            )
            ->where('mr.employee_id', $employeeId)
            ->whereYear('fp.first_paid', $year)
            ->whereMonth('fp.first_paid', $month)
            ->where('p.admin_fee_mode', 'per_registration')
            ->where('p.admin_fee_timing', 'on_first_payment')
            ->sum('p.admin_fee');

        // per_transaction: count each confirmed payment in period
        $total += (float) \DB::table('member_payments as mp')
            ->join('member_registrations as mr', 'mp.member_registration_id', '=', 'mr.id')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->where('mr.employee_id', $employeeId)
            ->whereYear('mp.paid_at', $year)
            ->whereMonth('mp.paid_at', $month)
            ->where('mp.payment_status', 'confirmed')
            ->where('p.admin_fee_mode', 'per_transaction')
            ->sum('p.admin_fee');

        return $total;
    }

    private function resolveRanking(Employee $employee, int $month, int $year, float $mpiScore): array
    {
        // Shared rank — employees with higher mpi_score get a lower (better) rank number
        // Rank = count of employees with strictly higher mpi_score + 1 (ties share rank)
        $areaId = $employee->branch?->area_id;
        $regionId = $employee->branch?->area?->region_id;

        $rankArea = $areaId ? $this->computeRank($month, $year, $mpiScore, 'area', $areaId) : null;
        $rankRegion = $regionId ? $this->computeRank($month, $year, $mpiScore, 'region', $regionId) : null;
        $rankNational = $this->computeRank($month, $year, $mpiScore, 'national');

        return [$rankArea, $rankRegion, $rankNational];
    }

    private function computeRank(int $month, int $year, float $mpiScore, string $scope, ?int $scopeId = null): int
    {
        $query = MarketingKpiSnapshot::where('period_month', $month)
            ->where('period_year', $year)
            ->where('mpi_score', '>', $mpiScore)
            ->whereIn('id', function ($sub) use ($month, $year) {
                $sub->selectRaw('MAX(id)')
                    ->from('marketing_kpi_snapshots')
                    ->where('period_month', $month)
                    ->where('period_year', $year)
                    ->groupBy('employee_id');
            });

        if ($scope === 'area' && $scopeId) {
            $query->whereHas('employee.branch', fn ($q) => $q->where('area_id', $scopeId));
        } elseif ($scope === 'region' && $scopeId) {
            $query->whereHas('employee.branch.area', fn ($q) => $q->where('region_id', $scopeId));
        }

        return $query->count() + 1;
    }
}
