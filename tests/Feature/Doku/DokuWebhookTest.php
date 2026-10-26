<?php

use App\Models\MemberPayment;
use App\Models\PaymentTransaction;
use App\Services\Doku\DokuSignature;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    config()->set('services.doku.client_id', 'CLIENT-X');
    config()->set('services.doku.secret_key', 'secret-key');
});

function postWebhook(array $body): \Illuminate\Testing\TestResponse
{
    $raw = json_encode($body, JSON_UNESCAPED_SLASHES);
    $sig = app(DokuSignature::class)->sign('req-wh', '2026-10-25T00:00:00Z', '/webhooks/doku', $raw);

    return test()->call('POST', '/webhooks/doku', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_CLIENT_ID' => 'CLIENT-X',
        'HTTP_REQUEST_ID' => 'req-wh',
        'HTTP_REQUEST_TIMESTAMP' => '2026-10-25T00:00:00Z',
        'HTTP_SIGNATURE' => $sig,
    ], $raw);
}

function seedPendingTx(int $amount = 1500000): PaymentTransaction
{
    $payment = MemberPayment::factory()->create(['amount' => $amount, 'payment_status' => 'pending']);

    return PaymentTransaction::create([
        'member_payment_id' => $payment->id,
        'invoice_number' => 'MP'.$payment->id.'-ABC123',
        'doku_request_id' => 'req-1',
        'amount' => $amount,
        'status' => 'pending',
    ]);
}

it('SUCCESS membuat payment paid dan transaction success', function () {
    $tx = seedPendingTx();
    $res = postWebhook(['transaction' => ['status' => 'SUCCESS'], 'order' => ['invoice_number' => $tx->invoice_number, 'amount' => 1500000]]);

    $res->assertOk();
    expect($tx->fresh()->status)->toBe('success');
    expect($tx->memberPayment->fresh()->payment_status)->toBe('paid');
});

it('duplikat SUCCESS idempotent (tetap sekali paid)', function () {
    $tx = seedPendingTx();
    $body = ['transaction' => ['status' => 'SUCCESS'], 'order' => ['invoice_number' => $tx->invoice_number, 'amount' => 1500000]];
    postWebhook($body)->assertOk();
    $paidAt = $tx->memberPayment->fresh()->paid_at;
    postWebhook($body)->assertOk();
    expect($tx->memberPayment->fresh()->paid_at->eq($paidAt))->toBeTrue();
});

it('signature salah ditolak 401', function () {
    $tx = seedPendingTx();
    $raw = json_encode(['transaction' => ['status' => 'SUCCESS'], 'order' => ['invoice_number' => $tx->invoice_number, 'amount' => 1500000]]);
    test()->call('POST', '/webhooks/doku', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_CLIENT_ID' => 'CLIENT-X',
        'HTTP_REQUEST_ID' => 'req-wh',
        'HTTP_REQUEST_TIMESTAMP' => '2026-10-25T00:00:00Z',
        'HTTP_SIGNATURE' => 'HMACSHA256=wrong',
    ], $raw)->assertStatus(401);
});

it('amount mismatch ditolak dan payment tidak paid', function () {
    $tx = seedPendingTx(1500000);
    postWebhook(['transaction' => ['status' => 'SUCCESS'], 'order' => ['invoice_number' => $tx->invoice_number, 'amount' => 999]])
        ->assertStatus(422);
    expect($tx->memberPayment->fresh()->payment_status)->toBe('pending');
});

it('FAILED diabaikan (payment tetap pending)', function () {
    $tx = seedPendingTx();
    postWebhook(['transaction' => ['status' => 'FAILED'], 'order' => ['invoice_number' => $tx->invoice_number, 'amount' => 1500000]])
        ->assertOk();
    expect($tx->memberPayment->fresh()->payment_status)->toBe('pending');
    expect($tx->fresh()->status)->toBe('pending');
});

it('tanpa header DOKU ditolak 401 tanpa crash', function () {
    $tx = seedPendingTx();
    $raw = json_encode(['transaction' => ['status' => 'SUCCESS'], 'order' => ['invoice_number' => $tx->invoice_number, 'amount' => 1500000]]);

    test()->call('POST', '/webhooks/doku', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
    ], $raw)->assertStatus(401);

    expect($tx->memberPayment->fresh()->payment_status)->toBe('pending');
});

it('EXPIRED menandai transaction expired dan membuka retry', function () {
    $tx = seedPendingTx();
    postWebhook(['transaction' => ['status' => 'EXPIRED'], 'order' => ['invoice_number' => $tx->invoice_number, 'amount' => 1500000]])
        ->assertOk();
    expect($tx->fresh()->status)->toBe('expired');
    expect($tx->memberPayment->fresh()->payment_status)->toBe('unpaid');
});
