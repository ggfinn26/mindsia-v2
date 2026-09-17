<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;

class EmployeeNotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'status' => 'unread',
        ];
    }
}
