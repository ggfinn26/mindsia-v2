<?php

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        return [
            'discount_code' => 'DISC-'.strtoupper(substr(md5(uniqid('', true)), 0, 8)),
            'is_active' => true,
            'discount_type' => 'fixed_amount',
            'discount_nominal' => 100000,
            'discount_quota' => null,
            'starts_at' => null,
            'expired_at' => null,
        ];
    }
}
