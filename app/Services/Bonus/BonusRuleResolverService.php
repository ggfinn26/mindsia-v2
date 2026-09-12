<?php

namespace App\Services\Bonus;

use App\Repositories\Bonus\KpiBonusRuleRepository;
use App\Repositories\Bonus\MarketingBonusRuleRepository;
use App\Repositories\Bonus\SpecialBonusRuleRepository;

class BonusRuleResolverService
{
    public function __construct(
        private readonly MarketingBonusRuleRepository $marketingRepo,
        private readonly KpiBonusRuleRepository $kpiRepo,
        private readonly SpecialBonusRuleRepository $specialRepo,
        private readonly BonusDataSourceService $dataSourceService,
    ) {}

    /**
     * Returns array of bonus breakdown items for payroll.
     * Each item has: type, rule_id, rule_code, rule_name, reward_type, reward_basis, reward_value, base_amount, calculated_amount.
     */
    public function resolveMarketingBonus(
        int $employeeId,
        int $positionId,
        int $roleId,
        int $periodYear,
        int $periodMonth,
        float $baseSalary,
        int $tenureMonths,
    ): array {
        $rules = $this->marketingRepo->findActiveForEmployee($employeeId, $positionId, $roleId);
        $achievement = $this->dataSourceService->resolveMarketingAchievement($employeeId, $periodYear, $periodMonth);
        $results = [];

        foreach ($rules as $rule) {
            $tier = $rule->tiers->first(fn ($t) => $tenureMonths >= $t->minimum_tenure_months
                && ($t->maximum_tenure_months === null || $tenureMonths <= $t->maximum_tenure_months)
                && $achievement >= (float) $t->minimum_achievement_percentage
                && ($t->maximum_achievement_percentage === null || $achievement <= (float) $t->maximum_achievement_percentage));

            if (! $tier) {
                continue;
            }

            ['base' => $base, 'amount' => $amount] = $this->calculateReward($rule->reward_basis, $tier->reward_type, (float) $tier->reward_value, $baseSalary);

            $results[] = [
                'type' => 'marketing',
                'rule_id' => $rule->id,
                'rule_code' => $rule->rule_code,
                'rule_name' => $rule->rule_name,
                'reward_type' => (string) $tier->reward_type,
                'reward_basis' => $rule->reward_basis,
                'reward_value' => (float) $tier->reward_value,
                'base_amount' => $base,
                'calculated_amount' => $amount,
            ];
        }

        return $results;
    }

    public function resolveKpiBonus(
        int $employeeId,
        int $positionId,
        int $roleId,
        float $kpiScore,
        float $baseSalary,
    ): array {
        $rules = $this->kpiRepo->findActiveForEmployee($employeeId, $positionId, $roleId);
        $results = [];

        foreach ($rules as $rule) {
            $tier = $rule->tiers->first(fn ($t) => $kpiScore >= (float) $t->minimum_score
                && ($t->maximum_score === null || $kpiScore <= (float) $t->maximum_score));

            if (! $tier) {
                continue;
            }

            ['base' => $base, 'amount' => $amount] = $this->calculateReward($rule->reward_basis, $tier->reward_type, (float) $tier->reward_value, $baseSalary);

            $results[] = [
                'type' => 'kpi',
                'rule_id' => $rule->id,
                'rule_code' => $rule->rule_code,
                'rule_name' => $rule->rule_name,
                'reward_type' => (string) $tier->reward_type,
                'reward_basis' => $rule->reward_basis,
                'reward_value' => (float) $tier->reward_value,
                'base_amount' => $base,
                'calculated_amount' => $amount,
            ];
        }

        return $results;
    }

    public function resolveSpecialBonus(
        int $employeeId,
        int $positionId,
        int $roleId,
        float $baseSalary,
        int $periodYear,
        int $periodMonth,
    ): array {
        $rules = $this->specialRepo->findActiveForEmployee($employeeId, $positionId, $roleId);
        $results = [];

        foreach ($rules as $rule) {
            $conditionResults = [];

            foreach ($rule->conditions as $cond) {
                $actual = $this->dataSourceService->resolveMetric($cond->data_source, $employeeId, $periodYear, $periodMonth);
                $passed = $actual !== null && $this->evaluate((float) $actual, $cond->operator, (float) $cond->target_value);

                $conditionResults[] = [
                    'metric_code' => $cond->metric_code,
                    'data_source' => $cond->data_source,
                    'operator' => $cond->operator,
                    'target_value' => (float) $cond->target_value,
                    'actual_value' => $actual,
                    'passed' => $passed,
                ];
            }

            $eligible = $rule->condition_mode === 'all'
                ? collect($conditionResults)->every(fn ($c) => $c['passed'])
                : collect($conditionResults)->contains(fn ($c) => $c['passed']);

            if (! $eligible) {
                continue;
            }

            ['base' => $base, 'amount' => $amount] = $this->calculateReward($rule->reward_basis, $rule->reward_type, (float) $rule->reward_value, $baseSalary);

            $results[] = [
                'type' => 'special',
                'rule_id' => $rule->id,
                'rule_code' => $rule->rule_code,
                'rule_name' => $rule->rule_name,
                'reward_type' => $rule->reward_type,
                'reward_basis' => $rule->reward_basis,
                'reward_value' => (float) $rule->reward_value,
                'base_amount' => $base,
                'calculated_amount' => $amount,
                'conditions' => $conditionResults,
            ];
        }

        return $results;
    }

    /** @return array{base: float, amount: float} */
    private function calculateReward(string $rewardBasis, string $rewardType, float $rewardValue, float $baseSalary): array
    {
        $base = $rewardBasis === 'base_salary' ? $baseSalary : 0.0;
        $amount = $rewardType === 'percentage' ? $base * $rewardValue / 100 : $rewardValue;

        return ['base' => $base, 'amount' => $amount];
    }

    private function evaluate(float $actual, string $operator, float $target): bool
    {
        return match ($operator) {
            '>=' => $actual >= $target,
            '<=' => $actual <= $target,
            '>' => $actual > $target,
            '<' => $actual < $target,
            '=' => $actual == $target,
            '!=' => $actual != $target,
            default => false,
        };
    }
}
