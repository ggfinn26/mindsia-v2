<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        return [
            'program_name' => $this->faker->words(2, true),
            'program_code' => 'PRG-'.substr(md5(uniqid('', true)), 0, 8),
            'program_price' => $this->faker->randomFloat(2, 100000, 5000000),
            'program_description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
