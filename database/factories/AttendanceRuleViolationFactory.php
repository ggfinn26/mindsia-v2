<?php

namespace Database\Factories;

use App\Models\AttendanceRule;
use App\Models\AttendanceRuleViolation;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceRuleViolationFactory extends Factory
{
    protected $model = AttendanceRuleViolation::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'attendance_rule_id' => AttendanceRule::factory(),
            'period_start_date' => now()->startOfMonth(),
            'period_end_date' => now()->endOfMonth(),
            'trigger_value' => $this->faker->randomFloat(2, 1, 10),
        ];
    }
}
