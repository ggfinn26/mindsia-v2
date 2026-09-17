<?php

namespace Database\Factories;

use App\Models\MarketingBonusRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketingBonusRuleFactory extends Factory
{
    protected $model = MarketingBonusRule::class;

    public function definition(): array
    {
        return [
            'rule_code' => $this->faker->unique()->bothify('MKT-####??'),
            'rule_name' => $this->faker->sentence(3),
            'scope_type' => 'global',
            'role_id' => null,
            'position_id' => null,
            'employee_id' => null,
            'bonus_basis' => 'marketing_mpi',
            'revenue_basis' => null,
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

    public function positionScope(int $positionId): self
    {
        return $this->state(fn () => [
            'scope_type' => 'position',
            'role_id' => null,
            'position_id' => $positionId,
            'employee_id' => null,
        ]);
    }

    public function employeeScope(int $employeeId): self
    {
        return $this->state(fn () => [
            'scope_type' => 'employee',
            'role_id' => null,
            'position_id' => null,
            'employee_id' => $employeeId,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
