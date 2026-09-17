<?php

namespace Database\Factories;

use App\Models\KpiBonusRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class KpiBonusRuleFactory extends Factory
{
    protected $model = KpiBonusRule::class;

    public function definition(): array
    {
        return [
            'rule_code' => $this->faker->unique()->bothify('KPI-####??'),
            'rule_name' => $this->faker->sentence(3),
            'scope_type' => 'global',
            'role_id' => null,
            'position_id' => null,
            'employee_id' => null,
            'reward_basis' => 'base_salary',
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

    public function roleScope(int $roleId): self
    {
        return $this->state(fn () => [
            'scope_type' => 'role',
            'role_id' => $roleId,
            'position_id' => null,
            'employee_id' => null,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
