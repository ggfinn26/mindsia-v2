<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\MemberData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MemberData>
 */
class MemberDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'birthdate' => $this->faker->date(),
            'whatsapp_number' => $this->faker->numerify('62###########'),
            'email' => $this->faker->unique()->safeEmail(),
            'institution_id' => Institution::factory(),
            'activation_status' => 'active',
        ];
    }
}
