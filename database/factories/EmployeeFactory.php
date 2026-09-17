<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $region = Region::factory()->create();
        $area = $region->areas()->first() ?? $region->areas()->create(['name' => 'Default Area']);

        return [
            'employee_code' => $this->faker->unique()->bothify('###??'),
            'full_name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'birthdate' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail(),
            'whatsapp_number' => $this->faker->numerify('62###########'),
            'region_id' => $region->id,
            'area_id' => $area->id,
            'branch_id' => Branch::factory()->create(['areas_id' => $area->id]),
            'is_active' => true,
        ];
    }
}
