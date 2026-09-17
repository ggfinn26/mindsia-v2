<?php

namespace Database\Factories;

use App\Models\AttendanceRule;
use App\Models\AttendanceRuleAction;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceRuleActionFactory extends Factory
{
    protected $model = AttendanceRuleAction::class;

    public function definition(): array
    {
        return [
            'attendance_rule_id' => AttendanceRule::factory(),
            'action_type' => 'notification',
            'action_order' => 1,
        ];
    }
}
