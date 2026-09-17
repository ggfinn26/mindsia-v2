<?php

namespace Database\Factories;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Holiday>
 */
class HolidayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d');

        return [
            'holiday_name' => fake()->words(2, true).' Holiday',
            'holiday_start_date' => $startDate,
            'holiday_end_date' => $startDate,
        ];
    }
}
