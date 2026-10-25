<?php

use App\Models\Discount;
use App\Models\MemberData;
use App\Models\Program;
use App\Models\ProgramPaymentPlan;
use App\Services\Member\MemberRegistrationService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('clamp diskon percentage agar final_price tidak negatif', function () {
    $program = Program::factory()->create(['program_price' => 1000000]);
    $plan = ProgramPaymentPlan::factory()->for($program)->create(['type' => 'cash', 'installment_count' => null, 'dp_amount' => 1000000, 'per_installment_amount' => null]);
    $member = MemberData::factory()->create();
    // nominal 150 hanya mungkin lolos jika request bypass; uji pertahanan service langsung
    $discount = Discount::factory()->create(['discount_type' => 'percentage', 'discount_nominal' => 150]);

    $reg = app(MemberRegistrationService::class)->register($member, [
        'program_id' => $program->id,
        'payment_plan_id' => $plan->id,
        'discount_code' => $discount->discount_code,
    ]);

    expect((int) $reg->final_price)->toBe(0);
    expect((int) $reg->discount_amount)->toBe(1000000);
});
