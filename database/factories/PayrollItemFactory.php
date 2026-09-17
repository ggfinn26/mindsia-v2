<?php

namespace Database\Factories;

use App\Models\PayrollItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollItem>
 */
class PayrollItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $componentType = fake()->randomElement(['earning', 'deduction']);

        return [
            'component_code_snapshot' => fake()->bothify('COMP-####'),
            'component_name_snapshot' => fake()->words(2, true),
            'component_type_snapshot' => $componentType,
            'quantity' => 1,
            'unit_value' => fake()->randomFloat(2, 100000, 5000000),
            'total_amount' => fake()->randomFloat(2, 100000, 5000000),
            'source_type' => 'compensation',
            'source_id' => null,
            'description' => null,
        ];
    }

    public function forPayroll(int $payrollId): static
    {
        return $this->state(fn (array $attributes) => [
            'employee_payroll_id' => $payrollId,
        ]);
    }

    public function forComponent(int $componentId): static
    {
        return $this->state(fn (array $attributes) => [
            'payroll_component_id' => $componentId,
        ]);
    }

    public function earning(): static
    {
        return $this->state(fn (array $attributes) => [
            'component_type_snapshot' => 'earning',
        ]);
    }

    public function deduction(): static
    {
        return $this->state(fn (array $attributes) => [
            'component_type_snapshot' => 'deduction',
        ]);
    }
}
