<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeLeaveRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeLeaveRequest>
 */
class EmployeeLeaveRequestFactory extends Factory
{
    protected $model = EmployeeLeaveRequest::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'branch_id' => Branch::factory(),
            'leave_type' => fake()->randomElement(['permission', 'sick', 'leave']),
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => fake()->sentence(),
            'status' => 'pending',
        ];
    }

    public function permission(): static
    {
        return $this->state(fn (array $attributes) => [
            'leave_type' => 'permission',
        ]);
    }

    public function sick(): static
    {
        return $this->state(fn (array $attributes) => [
            'leave_type' => 'sick',
        ]);
    }

    public function leave(): static
    {
        return $this->state(fn (array $attributes) => [
            'leave_type' => 'leave',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewed_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
