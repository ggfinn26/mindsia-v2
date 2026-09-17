<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeWorkAttendanceLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeWorkAttendanceLog>
 */
class EmployeeWorkAttendanceLogFactory extends Factory
{
    protected $model = EmployeeWorkAttendanceLog::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'branch_id' => Branch::factory(),
            'attendance_date' => now()->toDateString(),
            'status' => 'absent',
            'late_minutes' => 0,
            'early_leave_minutes' => 0,
            'is_location_anomaly' => false,
        ];
    }

    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'check_in' => now(),
            'status' => 'checked_in',
            'check_in_latitude' => -6.2,
            'check_in_longitude' => 106.8,
        ]);
    }

    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'check_in' => now()->setTime(8, 0),
            'check_out' => now()->setTime(17, 0),
            'status' => 'present',
            'check_in_latitude' => -6.2,
            'check_in_longitude' => 106.8,
            'check_out_latitude' => -6.2,
            'check_out_longitude' => 106.8,
        ]);
    }

    public function late(int $minutes = 30): static
    {
        return $this->state(fn (array $attributes) => [
            'check_in' => now(),
            'status' => 'late',
            'late_minutes' => $minutes,
            'check_in_latitude' => -6.2,
            'check_in_longitude' => 106.8,
        ]);
    }
}
