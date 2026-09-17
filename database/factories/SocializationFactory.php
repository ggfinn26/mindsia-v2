<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Institution;
use App\Models\Socialization;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocializationFactory extends Factory
{
    protected $model = Socialization::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'area_id' => Area::factory(),
            'institution_id' => Institution::factory(),
            'location_name' => $this->faker->company(),
            'partner_fee_amount' => $this->faker->randomElement([null, 100000, 500000]),
            'partner_fee_status' => 'none',
            'status' => Socialization::STATUS_DRAFT,
            'created_by_employee_id' => Employee::factory(),
        ];
    }
}
