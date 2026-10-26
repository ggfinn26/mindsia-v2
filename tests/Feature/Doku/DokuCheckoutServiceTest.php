<?php

use App\Models\MemberPayment;
use App\Services\Doku\DokuCheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('services.doku.client_id', 'CLIENT-X');
    config()->set('services.doku.secret_key', 'secret-key');
    config()->set('services.doku.base_url', 'https://api-sandbox.doku.com');
    config()->set('services.doku.notification_url', 'https://staging.test/webhooks/doku');
});

it('membuat checkout, menyimpan transaction pending, dan set member_payment pending', function () {
    Http::fake([
        'api-sandbox.doku.com/checkout/v1/payment' => Http::response([
            'response' => [
                'payment' => [
                    'url' => 'https://sandbox.doku.com/checkout-link-v2/abc',
                    'token_id' => 'tok-123',
                    'expired_date' => '20261025120000',
                ],
                'order' => ['session_id' => 'sess-1'],
            ],
        ], 200),
    ]);

    $payment = MemberPayment::factory()->create(['amount' => 1500000]);

    $url = app(DokuCheckoutService::class)->createCheckout($payment);

    expect($url)->toBe('https://sandbox.doku.com/checkout-link-v2/abc');

    $tx = $payment->transactions()->first();
    expect($tx)->not->toBeNull();
    expect($tx->status)->toBe('pending');
    expect($tx->amount)->toBe(1500000);
    expect($tx->checkout_url)->toBe('https://sandbox.doku.com/checkout-link-v2/abc');
    expect($payment->fresh()->payment_status)->toBe('pending');

    Http::assertSent(function ($request) {
        return $request->hasHeader('Client-Id', 'CLIENT-X')
            && $request->hasHeader('Signature')
            && $request['order']['amount'] === 1500000
            && ! empty($request['order']['callback_url'])
            && $request['order']['auto_redirect'] === true;
    });
});

it('melempar exception saat DOKU balas error', function () {
    Http::fake(['api-sandbox.doku.com/*' => Http::response(['error' => ['message' => 'Bad']], 400)]);
    $payment = MemberPayment::factory()->create(['amount' => 1500000]);

    app(DokuCheckoutService::class)->createCheckout($payment);
})->throws(RuntimeException::class);
