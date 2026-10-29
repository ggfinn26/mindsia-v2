<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\ProgramPaymentPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramPaymentPlanFactory extends Factory
{
    protected $model = ProgramPaymentPlan::class;

    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'label' => 'Plan '.$this->faker->word(),
            'type' => 'installment',
            'installment_count' => 4,
            'dp_amount' => 4000000,
            'per_installment_amount' => 2000000,
            'is_active' => true,
        ];
    }
}
