<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;
use App\Models\Position;

class EmploymentStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'employees_id' => Employee::factory(),
            'type_employment' => 'TETAP',
            'join_date' => now()->subYear(),
            'position_id' => Position::factory(),
        ];
    }
}
