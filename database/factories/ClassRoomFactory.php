<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\Employee;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassRoomFactory extends Factory
{
    protected $model = ClassRoom::class;

    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'branch_id' => Branch::factory(),
            'class_name' => $this->faker->bothify('Class-##'),
            'tutor_id' => Employee::factory(),
            'day_of_week' => $this->faker->randomElement(ClassRoom::VALID_DAYS),
            'week_count' => $this->faker->numberBetween(4, 12),
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonths(3)->endOfMonth(),
            'start_time_primary' => '09:00',
            'end_time_primary' => '11:00',
            'start_time_secondary' => null,
            'end_time_secondary' => null,
            'status' => 'active',
        ];
    }
}
