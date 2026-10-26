<?php

use App\Actions\Member\MarkMemberPaymentPaid;
use App\Models\MemberPayment;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('menandai payment paid dan idempotent saat dipanggil ulang', function () {
    $payment = MemberPayment::factory()->create(['payment_status' => 'pending', 'amount' => 1500000]);

    $action = app(MarkMemberPaymentPaid::class);
    $first = $action->handle($payment->fresh(), 'doku', 'INV-1');
    $paidAt = $payment->fresh()->paid_at;

    expect($first)->toBeTrue();
    expect($payment->fresh()->payment_status)->toBe('paid');
    expect($payment->fresh()->payment_method)->toBe('doku');

    // panggil ulang: tidak mengubah paid_at, return false (sudah paid)
    $second = $action->handle($payment->fresh(), 'doku', 'INV-1');
    expect($second)->toBeFalse();
    expect($payment->fresh()->paid_at->eq($paidAt))->toBeTrue();
});
