<?php

namespace Database\Factories;

use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeePayrollPayment>
 */
class EmployeePayrollPaymentFactory extends Factory
{
    protected $model = EmployeePayrollPayment::class;

    public function definition(): array
    {
        return [
            'employee_payroll_id' => EmployeePayroll::factory(),
            'payment_status' => 'paid',
            'payment_method' => $this->faker->randomElement(['bank_transfer', 'cash']),
            'amount' => $this->faker->randomFloat(2, 100000, 10000000),
            'paid_at' => now(),
            'payment_reference' => $this->faker->optional()->uuid(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
