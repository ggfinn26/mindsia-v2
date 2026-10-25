<?php

use App\Models\MemberData;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Models\Program;
use App\Models\ProgramPaymentPlan;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeDiscountedRegistration(string $paymentStatus): MemberRegistration
{
    $program = Program::factory()->create(['program_price' => 10000000]);
    $plan = ProgramPaymentPlan::factory()->for($program)->create(['type' => 'installment', 'installment_count' => 4, 'dp_amount' => 4000000, 'per_installment_amount' => 2000000]);
    $member = MemberData::factory()->create();

    return MemberRegistration::create([
        'members_data_id' => $member->id,
        'program_id' => $program->id,
        'employee_id' => null,
        'receipt_member_name' => $member->full_name,
        'receipt_institution_name' => '',
        'receipt_program_name' => $program->program_name,
        'original_price' => 10000000,
        'discount_amount' => 2000000,
        'final_price' => 8000000,
        'installment_type' => '4',
        'payment_plan_id' => $plan->id,
        'receipt_payment_plan_label' => $plan->label,
        'graduation_status' => 'BELUM_LULUS',
        'payment_status' => $paymentStatus,
    ]);
}

it('resync cicilan unpaid registrasi berdiskon ke final_price', function () {
    $reg = makeDiscountedRegistration('unpaid');

    foreach ([4000000, 2000000, 2000000, 2000000] as $i => $amount) {
        MemberPayment::create([
            'member_registration_id' => $reg->id,
            'installment_number' => $i + 1,
            'amount' => $amount,
            'payment_status' => 'unpaid',
        ]);
    }

    $this->artisan('member:resync-installments')->assertExitCode(0);

    $amounts = $reg->payments()->orderBy('installment_number')->pluck('amount')->map(fn ($a) => (int) $a)->all();
    expect(array_sum($amounts))->toBe(8000000);
});

it('skip registrasi yang punya cicilan sudah paid', function () {
    $reg = makeDiscountedRegistration('installments');
    MemberPayment::create(['member_registration_id' => $reg->id, 'installment_number' => 1, 'amount' => 4000000, 'payment_status' => 'paid']);

    $this->artisan('member:resync-installments')->assertExitCode(0);

    expect((int) $reg->payments()->where('installment_number', 1)->first()->amount)->toBe(4000000);
});
