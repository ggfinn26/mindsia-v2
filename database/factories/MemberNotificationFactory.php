<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MemberData;

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
