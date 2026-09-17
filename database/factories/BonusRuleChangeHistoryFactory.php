<?php

namespace Database\Factories;

use App\Models\BonusRuleChangeHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BonusRuleChangeHistoryFactory extends Factory
{
    protected $model = BonusRuleChangeHistory::class;

    public function definition(): array
    {
        return [
            'bonus_type' => $this->faker->randomElement(['marketing', 'kpi', 'special']),
            'rule_id' => 1,
            'action' => $this->faker->randomElement(['created', 'updated', 'deleted', 'deactivated']),
            'old_values' => null,
            'new_values' => ['rule_name' => $this->faker->sentence(3)],
            'changed_by_employee_id' => null,
        ];
    }
}
