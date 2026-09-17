<?php

namespace Database\Factories;

use App\Models\PayrollComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollComponent>
 */
class PayrollComponentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'component_code' => 'TEST-'.strtoupper(fake()->unique()->lexify('????')),
            'component_name' => fake()->words(2, true),
            'component_type' => fake()->randomElement(['earning', 'deduction']),
            'calculation_method' => fake()->randomElement(['fixed', 'daily', 'session', 'percentage', 'manual']),
            'is_taxable' => fake()->boolean(),
            'is_active' => true,
        ];
    }

    public function earning(): static
    {
        return $this->state(fn (array $attributes) => [
            'component_type' => 'earning',
        ]);
    }

    public function deduction(): static
    {
        return $this->state(fn (array $attributes) => [
            'component_type' => 'deduction',
        ]);
    }

    public function fixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'calculation_method' => 'fixed',
        ]);
    }

    public function daily(): static
    {
        return $this->state(fn (array $attributes) => [
            'calculation_method' => 'daily',
        ]);
    }

    public function session(): static
    {
        return $this->state(fn (array $attributes) => [
            'calculation_method' => 'session',
        ]);
    }

    public function percentage(): static
    {
        return $this->state(fn (array $attributes) => [
            'calculation_method' => 'percentage',
        ]);
    }

    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'calculation_method' => 'manual',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
