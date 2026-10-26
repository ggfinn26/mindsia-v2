<?php

namespace Database\Factories;

use App\Models\MemberData;
use App\Models\MemberRegistration;
use App\Models\Program;
use App\Models\ProgramPaymentPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberRegistrationFactory extends Factory
{
    protected $model = MemberRegistration::class;

    public function definition(): array
    {
        return [
            'members_data_id' => MemberData::factory(),
            'program_id' => Program::factory(),
            'employee_id' => null,
            'receipt_member_name' => $this->faker->name(),
            'receipt_institution_name' => $this->faker->company(),
            'receipt_program_name' => $this->faker->words(2, true),
            'original_price' => 1500000,
            'discount_amount' => 0,
            'final_price' => 1500000,
            'installment_type' => '1',
            'payment_plan_id' => ProgramPaymentPlan::factory(),
            'receipt_payment_plan_label' => 'Cash',
            'graduation_status' => 'BELUM_LULUS',
            'payment_status' => 'unpaid',
        ];
    }
}
