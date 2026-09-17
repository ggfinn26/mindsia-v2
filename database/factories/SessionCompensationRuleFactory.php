<?php

namespace Database\Factories;

use App\Models\SessionCompensationRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SessionCompensationRule>
 */
class SessionCompensationRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rule_code' => fake()->unique()->bothify('SESS-####'),
            'rule_name' => fake()->words(2, true),
            'scope_type' => 'global',
            'role_id' => null,
            'position_id' => null,
            'employee_id' => null,
            'amount_per_session' => fake()->randomFloat(2, 10000, 100000),
            'is_active' => true,
            'notes' => null,
        ];
    }

    public function globalScope(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope_type' => 'global',
            'role_id' => null,
            'position_id' => null,
            'employee_id' => null,
        ]);
    }

    public function roleScope(int $roleId): static
    {
        return $this->state(fn (array $attributes) => [
            'scope_type' => 'role',
            'role_id' => $roleId,
            'position_id' => null,
            'employee_id' => null,
        ]);
    }

    public function positionScope(int $positionId): static
    {
        return $this->state(fn (array $attributes) => [
            'scope_type' => 'position',
            'role_id' => null,
            'position_id' => $positionId,
            'employee_id' => null,
        ]);
    }

    public function employeeScope(int $employeeId): static
    {
        return $this->state(fn (array $attributes) => [
            'scope_type' => 'employee',
            'role_id' => null,
            'position_id' => null,
            'employee_id' => $employeeId,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
