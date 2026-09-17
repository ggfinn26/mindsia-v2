<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'template_key' => $this->faker->unique()->slug,
            'type' => $this->faker->randomElement(['email', 'telegram', 'in-app']),
            'subject' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
        ];
    }
}
