<?php

use App\Models\MemberPayment;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    config()->set('services.doku.client_id', 'CLIENT-X');
    config()->set('services.doku.secret_key', 'secret-key');
    config()->set('services.doku.base_url', 'https://api-sandbox.doku.com');
});

it('poll menandai SUCCESS dari check-status dan flip payment paid', function () {
    $payment = MemberPayment::factory()->create(['amount' => 1500000, 'payment_status' => 'pending']);
    $tx = PaymentTransaction::create([
        'member_payment_id' => $payment->id, 'invoice_number' => 'MP'.$payment->id.'-X', 'doku_request_id' => 'r', 'amount' => 1500000, 'status' => 'pending', 'expired_at' => now()->subMinute(),
    ]);

    Http::fake(['api-sandbox.doku.com/orders/v1/status/*' => Http::response([
        'transaction' => ['status' => 'SUCCESS'], 'order' => ['invoice_number' => $tx->invoice_number, 'amount' => 1500000],
    ], 200)]);

    test()->artisan('doku:sync-pending')->assertExitCode(0);

    expect($tx->fresh()->status)->toBe('success');
    expect($payment->fresh()->payment_status)->toBe('paid');
});

it('poll menandai EXPIRED dan membuka retry', function () {
    $payment = MemberPayment::factory()->create(['amount' => 1500000, 'payment_status' => 'pending']);
    $tx = PaymentTransaction::create([
        'member_payment_id' => $payment->id, 'invoice_number' => 'MP'.$payment->id.'-Y', 'doku_request_id' => 'r', 'amount' => 1500000, 'status' => 'pending', 'expired_at' => now()->subMinute(),
    ]);

    Http::fake(['api-sandbox.doku.com/orders/v1/status/*' => Http::response([
        'transaction' => ['status' => 'EXPIRED'], 'order' => ['invoice_number' => $tx->invoice_number, 'amount' => 1500000],
    ], 200)]);

    test()->artisan('doku:sync-pending')->assertExitCode(0);

    expect($tx->fresh()->status)->toBe('expired');
    expect($payment->fresh()->payment_status)->toBe('unpaid');
});
