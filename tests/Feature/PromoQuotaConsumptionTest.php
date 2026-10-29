<?php

use App\Models\Discount;
use App\Models\MemberData;
use App\Models\Program;
use App\Models\ProgramPaymentPlan;
use App\Services\Member\MemberRegistrationService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function registerWithQuotaDiscount(Discount $discount): \App\Models\MemberRegistration
{
    $program = Program::factory()->create(['program_price' => 1000000]);
    $plan = ProgramPaymentPlan::factory()->for($program)->create(['type' => 'installment', 'installment_count' => 2, 'dp_amount' => 500000, 'per_installment_amount' => 500000]);
    $member = MemberData::factory()->create();

    return app(MemberRegistrationService::class)->register($member, [
        'program_id' => $program->id,
        'payment_plan_id' => $plan->id,
        'discount_code' => $discount->discount_code,
    ]);
}

it('tidak mengurangi quota saat registrasi', function () {
    $discount = Discount::factory()->create(['discount_quota' => 5, 'discount_type' => 'fixed_amount', 'discount_nominal' => 100000]);
    registerWithQuotaDiscount($discount);
    expect((int) $discount->fresh()->discount_quota)->toBe(5);
});

it('mengurangi quota saat consumePromoQuota dipanggil (titik bayar pertama)', function () {
    $discount = Discount::factory()->create(['discount_quota' => 5, 'discount_type' => 'fixed_amount', 'discount_nominal' => 100000]);
    $reg = registerWithQuotaDiscount($discount);

    app(MemberRegistrationService::class)->consumePromoQuota($reg->fresh());

    expect((int) $discount->fresh()->discount_quota)->toBe(4);
    expect($reg->fresh()->promo_quota_consumed)->toBeTrue();
});

it('hanya mengurangi quota sekali meski dipanggil berulang', function () {
    $discount = Discount::factory()->create(['discount_quota' => 5, 'discount_type' => 'fixed_amount', 'discount_nominal' => 100000]);
    $reg = registerWithQuotaDiscount($discount);
    $service = app(MemberRegistrationService::class);

    $service->consumePromoQuota($reg->fresh());
    $service->consumePromoQuota($reg->fresh());
    $service->consumePromoQuota($reg->fresh());

    expect((int) $discount->fresh()->discount_quota)->toBe(4);
});

it('registrasi tanpa diskon aman saat consumePromoQuota dipanggil', function () {
    $program = Program::factory()->create(['program_price' => 1000000]);
    $plan = ProgramPaymentPlan::factory()->for($program)->create(['type' => 'cash', 'installment_count' => null, 'dp_amount' => 1000000, 'per_installment_amount' => null]);
    $member = MemberData::factory()->create();
    $reg = app(MemberRegistrationService::class)->register($member, ['program_id' => $program->id, 'payment_plan_id' => $plan->id]);

    app(MemberRegistrationService::class)->consumePromoQuota($reg->fresh());

    expect($reg->fresh()->promo_quota_consumed)->toBeFalse();
});
