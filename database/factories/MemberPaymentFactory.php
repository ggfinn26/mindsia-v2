<?php

namespace Database\Factories;

use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberPaymentFactory extends Factory
{
    protected $model = MemberPayment::class;

    public function definition(): array
    {
        return [
            'member_registration_id' => MemberRegistration::factory(),
            'installment_number' => 1,
            'amount' => 1500000,
            'payment_status' => 'unpaid',
        ];
    }
}
