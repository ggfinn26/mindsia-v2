<?php

use App\Models\Discount;
use App\Models\MemberData;
use App\Models\Program;
use App\Models\ProgramPaymentPlan;
use App\Services\Member\MemberRegistrationService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function registerWith(array $planAttrs, ?array $discountAttrs = null): \App\Models\MemberRegistration
{
    $program = Program::factory()->create(['program_price' => 10000000]);
    $plan = ProgramPaymentPlan::factory()->for($program)->create($planAttrs);
    $member = MemberData::factory()->create();

    $data = ['program_id' => $program->id, 'payment_plan_id' => $plan->id];
    if ($discountAttrs !== null) {
        $data['discount_code'] = Discount::factory()->create($discountAttrs)->discount_code;
    }

    return app(MemberRegistrationService::class)->register($member, $data);
}

it('menyerap diskon ke DP sehingga total cicilan == final_price', function () {
    $reg = registerWith(
        ['type' => 'installment', 'installment_count' => 4, 'dp_amount' => 4000000, 'per_installment_amount' => 2000000],
        ['discount_type' => 'fixed_amount', 'discount_nominal' => 2000000],
    );

    $amounts = $reg->payments()->orderBy('installment_number')->pluck('amount')->map(fn ($a) => (int) $a)->all();

    expect($amounts)->toBe([2000000, 2000000, 2000000, 2000000]);
    expect(array_sum($amounts))->toBe((int) $reg->final_price)->toBe(8000000);
});

it('luber ke cicilan berikut saat diskon lebih besar dari DP', function () {
    $reg = registerWith(
        ['type' => 'installment', 'installment_count' => 4, 'dp_amount' => 4000000, 'per_installment_amount' => 2000000],
        ['discount_type' => 'fixed_amount', 'discount_nominal' => 5000000],
    );

    $amounts = $reg->payments()->orderBy('installment_number')->pluck('amount')->map(fn ($a) => (int) $a)->all();

    expect($amounts)->toBe([0, 1000000, 2000000, 2000000]);
    expect(array_sum($amounts))->toBe((int) $reg->final_price)->toBe(5000000);
});

it('cash plan membuat satu cicilan sebesar final_price', function () {
    $reg = registerWith(
        ['type' => 'cash', 'installment_count' => null, 'dp_amount' => 10000000, 'per_installment_amount' => null],
        ['discount_type' => 'fixed_amount', 'discount_nominal' => 3000000],
    );

    $amounts = $reg->payments()->pluck('amount')->map(fn ($a) => (int) $a)->all();

    expect($amounts)->toBe([7000000]);
});
