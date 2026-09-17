<?php

namespace Database\Factories;

use App\Models\SpecialBonusRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialBonusRuleFactory extends Factory
{
    protected $model = SpecialBonusRule::class;

    public function definition(): array
    {
        return [
            'rule_code' => $this->faker->unique()->bothify('SPC-####??'),
            'rule_name' => $this->faker->sentence(3),
            'scope_type' => 'global',
            'role_id' => null,
            'position_id' => null,
            'employee_id' => null,
            'reward_type' => 'fixed',
            'reward_value' => $this->faker->randomFloat(2, 100, 5000),
            'reward_basis' => 'base_salary',
            'condition_mode' => 'all',
            'is_active' => true,
            'notes' => null,
        ];
    }

    public function globalScope(): self
    {
        return $this->state(fn () => [
            'scope_type' => 'global',
            'role_id' => null,
            'position_id' => null,
            'employee_id' => null,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
