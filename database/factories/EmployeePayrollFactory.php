<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeePayroll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeePayroll>
 */
class EmployeePayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'employee_code_snapshot' => fake()->bothify('EMP-####'),
            'employee_name_snapshot' => fake()->name(),
            'position_name_snapshot' => fake()->word(),
            'branch_name_snapshot' => fake()->city(),
            'scheduled_working_days' => 22,
            'effective_working_days' => 22,
            'days_present' => 20,
            'days_absent' => 2,
            'days_sick' => 0,
            'days_permission' => 0,
            'days_leave' => 0,
            'days_holiday' => 0,
            'days_late' => 0,
            'total_sessions' => 22,
            'attended_sessions' => 20,
            'absent_sessions' => 2,
            'late_sessions' => 0,
            'total_earnings' => fake()->randomFloat(2, 3000000, 15000000),
            'total_deductions' => fake()->randomFloat(2, 100000, 3000000),
            'net_amount' => fake()->randomFloat(2, 2000000, 12000000),
            'payment_status' => 'unpaid',
            'paid_at' => null,
            'payment_reference' => null,
            'notes' => null,
        ];
    }

    public function forPeriod(int $periodId): static
    {
        return $this->state(fn (array $attributes) => [
            'payroll_period_id' => $periodId,
        ]);
    }

    public function forEmployee(int $employeeId): static
    {
        return $this->state(fn (array $attributes) => [
            'employee_id' => $employeeId,
        ]);
    }

    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'unpaid',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
