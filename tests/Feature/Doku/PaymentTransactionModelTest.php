<?php

use App\Models\MemberPayment;
use App\Models\PaymentTransaction;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('payment_transaction belongsTo member_payment dan cast benar', function () {
    $payment = MemberPayment::factory()->create();
    $tx = PaymentTransaction::create([
        'member_payment_id' => $payment->id,
        'invoice_number' => 'MP'.$payment->id.'-ABC123',
        'doku_request_id' => 'req-1',
        'amount' => 1500000,
        'status' => 'pending',
    ]);

    expect($tx->memberPayment->id)->toBe($payment->id);
    expect($tx->amount)->toBeInt();
    expect($payment->transactions)->toHaveCount(1);
});
