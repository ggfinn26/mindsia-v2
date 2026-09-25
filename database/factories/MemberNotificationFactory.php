<?php

namespace Database\Factories;

use App\Models\MemberData;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberNotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'member_id' => MemberData::factory(),
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'status' => 'unread',
        ];
    }
}
