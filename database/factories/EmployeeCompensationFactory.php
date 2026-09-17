<?php

namespace Database\Factories;

use App\Models\EmployeeCompensation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeCompensation>
 */
class EmployeeCompensationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => fake()->randomFloat(2, 100000, 10000000),
            'notes' => null,
        ];
    }

    public function forEmployee(int $employeeId): static
    {
        return $this->state(fn (array $attributes) => [
            'employee_id' => $employeeId,
        ]);
    }

    public function forComponent(int $componentId): static
    {
        return $this->state(fn (array $attributes) => [
            'payroll_component_id' => $componentId,
        ]);
    }
}
