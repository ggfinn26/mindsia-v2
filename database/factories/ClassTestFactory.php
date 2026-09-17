<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use App\Models\ClassTest;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassTestFactory extends Factory
{
    protected $model = ClassTest::class;

    public function definition(): array
    {
        return [
            'test_name' => 'Test '.$this->faker->word(),
            'test_type' => $this->faker->randomElement(['pre_test', 'post_test']),
            'program_id' => Program::factory(),
            'class_id' => ClassRoom::factory(),
            'date' => today()->toDateString(),
            'description' => null,
        ];
    }
}
