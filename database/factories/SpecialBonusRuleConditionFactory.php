<?php

namespace Database\Factories;

use App\Models\SpecialBonusRule;
use App\Models\SpecialBonusRuleCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialBonusRuleConditionFactory extends Factory
{
    protected $model = SpecialBonusRuleCondition::class;

    public function definition(): array
    {
        return [
            'special_bonus_rule_id' => SpecialBonusRule::factory(),
            'metric_code' => $this->faker->randomElement(['attendance_days_present', 'revenue_total', 'member_count', 'class_session_count']),
            'period_type' => $this->faker->randomElement(['payroll_period', 'monthly', 'quarterly']),
            'operator' => $this->faker->randomElement(['>=', '<=', '=', '>', '<', '!=']),
            'target_value' => $this->faker->randomFloat(2, 1, 100),
            'data_source' => $this->faker->randomElement(['attendance_days_present', 'revenue_total', 'member_count', 'class_session_count']),
            'is_active' => true,
            'notes' => null,
        ];
    }

    public function forRule(int $ruleId): self
    {
        return $this->state(fn () => ['special_bonus_rule_id' => $ruleId]);
    }
}
