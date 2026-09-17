<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Institution>
 */
class InstitutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'regions_id' => Region::factory(),
            'institution_name' => $this->faker->unique()->company(),
            'jenjang_institution' => $this->faker->randomElement(['SD', 'SMP', 'SMA', 'PERGURUAN TINGGI']),
        ];
    }
}
