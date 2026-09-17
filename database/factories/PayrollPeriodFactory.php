<?php

namespace Database\Factories;

use App\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollPeriod>
 */
class PayrollPeriodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Use faker unique on a single sequence to guarantee unique (year, month) pairs
        // Single pool avoids exhaustion; derive month/year from the sequence value.
        // Start at 25080 (= 2090*12 + 1) to avoid collisions with seeder (2025-2027).
        $seq = fake()->unique()->numberBetween(25080, 30000);
        $year = intdiv($seq, 12);
        $month = ($seq % 12) + 1;

        return [
            'period_month' => $month,
            'period_year' => $year,
            'status' => 'draft',
            'pay_date' => null,
            'confirmed_by_employee_id' => null,
            'confirmed_at' => null,
            'notes' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function review(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'review',
        ]);
    }

    public function finalized(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'finalized',
        ]);
    }
}
