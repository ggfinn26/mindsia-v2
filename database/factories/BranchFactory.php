<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'areas_id' => Area::factory(),
            'branch_name' => $this->faker->company(),
            'code_branches' => $this->faker->unique()->bothify('BR-????'),
            'address' => $this->faker->address(),
            'whatsapp' => $this->faker->numerify('62###########'),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'radius_meters' => 500,
            'is_active' => true,
        ];
    }
}
