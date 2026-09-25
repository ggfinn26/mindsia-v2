<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

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
