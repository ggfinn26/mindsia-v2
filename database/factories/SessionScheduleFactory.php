<?php

namespace Database\Factories;

use App\Models\ClassSchedule;
use App\Models\Employee;
use App\Models\SessionSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionScheduleFactory extends Factory
{
    protected $model = SessionSchedule::class;

    public function definition(): array
    {
        return [
            'class_schedule_id' => ClassSchedule::factory(),
            'employee_id' => Employee::factory(),
        ];
    }
}
