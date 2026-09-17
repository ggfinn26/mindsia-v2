<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassScheduleFactory extends Factory
{
    protected $model = ClassSchedule::class;

    public function definition(): array
    {
        return [
            'class_id' => ClassRoom::factory(),
            'schedule_date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'late_tolerance_minutes' => 15,
            'material_taught' => $this->faker->sentence(3),
        ];
    }

    public function withLateTolerance(int $minutes): static
    {
        return $this->state(fn (array $attributes) => [
            'late_tolerance_minutes' => $minutes,
        ]);
    }
}
