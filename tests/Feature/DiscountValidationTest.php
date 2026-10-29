<?php

use App\Models\Discount;
use App\Models\MemberData;
use App\Models\Program;
use App\Models\ProgramPaymentPlan;
use App\Services\Member\MemberRegistrationService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function registerUsingDiscount(array $discountAttrs): void
{
    $program = Program::factory()->create(['program_price' => 1000000]);
    $plan = ProgramPaymentPlan::factory()->for($program)->create(['type' => 'cash', 'installment_count' => null, 'dp_amount' => 1000000, 'per_installment_amount' => null]);
    $member = MemberData::factory()->create();
    $discount = Discount::factory()->create($discountAttrs);

    app(MemberRegistrationService::class)->register($member, [
        'program_id' => $program->id,
        'payment_plan_id' => $plan->id,
        'discount_code' => $discount->discount_code,
    ]);
}

it('menolak diskon nonaktif', function () {
    registerUsingDiscount(['is_active' => false]);
})->throws(RuntimeException::class, 'Diskon tidak aktif.');

it('menolak diskon yang belum mulai', function () {
    registerUsingDiscount(['starts_at' => now()->addDay()]);
})->throws(RuntimeException::class, 'Diskon belum berlaku.');
