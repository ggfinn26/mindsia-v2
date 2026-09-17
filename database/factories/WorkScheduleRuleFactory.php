<?php

namespace Database\Factories;

use App\Models\WorkScheduleRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkScheduleRule>
 */
class WorkScheduleRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'setting_name' => 'Shift '.fake()->unique()->word(),
            'start_time' => '08:00',
            'end_time' => '17:00',
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
            'late_tolerance_minutes' => 15,
            'early_leave_tolerance_minutes' => 15,
            'is_required' => true,
            'is_active' => true,
        ];
    }
}
