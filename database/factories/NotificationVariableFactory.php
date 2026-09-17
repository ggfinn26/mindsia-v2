<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\NotificationTemplate;

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
