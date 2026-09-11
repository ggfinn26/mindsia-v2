<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class AreaFactory extends Factory
{
    protected $model = Area::class;

    public function definition(): array
    {
        return [
            'region_id' => Region::factory(),
            'name' => $this->faker->city(),
        ];
    }
}
