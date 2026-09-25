<?php

namespace Database\Factories;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationVariableFactory extends Factory
{
    public function definition(): array
    {
        return [
            'notification_template_id' => NotificationTemplate::factory(),
            'variable_name' => $this->faker->word,
            'variable_type' => $this->faker->randomElement(['string', 'integer', 'date']),
            'variable_description' => $this->faker->sentence,
        ];
    }
}
