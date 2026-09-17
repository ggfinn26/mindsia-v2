<?php

namespace Database\Factories;

use App\Models\AttendanceRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceRuleFactory extends Factory
{
    protected $model = AttendanceRule::class;

    public function definition(): array
    {
        return [
            'rule_name' => $this->faker->words(2, true),
            'attendance_type' => 'work_schedule',
            'trigger_type' => 'consecutive_absence',
            'trigger_operator' => '>=',
            'trigger_value' => 3,
            'period_type' => 'monthly',
            'is_active' => true,
        ];
    }
}
