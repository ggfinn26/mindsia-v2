<?php

namespace App\Services\Doku;

use App\Models\MemberPayment;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuCheckoutService
{
    private const TARGET = '/checkout/v1/payment';

    public function __construct(private readonly DokuSignature $signature) {}

    public function createCheckout(MemberPayment $payment): string
    {
        $invoice = 'MP'.$payment->id.'-'.strtoupper(Str::random(6));
        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $amount = (int) $payment->amount;

        $member = $payment->registration?->memberData;

        $body = [
            'order' => [
                'amount' => $amount,
                'invoice_number' => $invoice,
                // Redirect browser balik ke halaman cicilan setelah bayar; auto_redirect
                // melewati halaman "Back to Merchant" DOKU. Status tetap di-update via webhook.
                'callback_url' => route('member.payment.installments'),
                'auto_redirect' => true,
            ],
            'payment' => [
                'payment_due_date' => 60,
            ],
            'customer' => array_filter([
                'name' => $member?->full_name,
                'email' => $member?->email,
                'phone' => $member?->whatsapp_number,
            ]),
            'additional_info' => array_filter([
                'override_notification_url' => config('services.doku.notification_url'),
            ]),
        ];

        $rawBody = json_encode($body, JSON_UNESCAPED_SLASHES);
        $sig = $this->signature->sign($requestId, $timestamp, self::TARGET, $rawBody);

        $response = Http::withHeaders([
            'Client-Id' => $this->signature->clientId(),
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $sig,
            'Content-Type' => 'application/json',
        ])->withBody($rawBody, 'application/json')
            ->post(rtrim((string) config('services.doku.base_url'), '/').self::TARGET);

        if ($response->failed()) {
            throw new \RuntimeException('DOKU create checkout gagal: '.$response->status().' '.$response->body());
        }

        $payload = $response->json('response') ?? [];
        $url = data_get($payload, 'payment.url');

        if (! $url) {
            throw new \RuntimeException('DOKU response tanpa payment.url.');
        }

        DB::transaction(function () use ($payment, $invoice, $requestId, $amount, $payload, $url) {
            PaymentTransaction::create([
                'member_payment_id' => $payment->id,
                'invoice_number' => $invoice,
                'doku_request_id' => $requestId,
                'amount' => $amount,
                'status' => 'pending',
                'checkout_url' => $url,
                'token_id' => data_get($payload, 'payment.token_id'),
                'expired_at' => $this->parseExpiredDate(data_get($payload, 'payment.expired_date')),
                'raw_response' => $payload,
            ]);

            $payment->update(['payment_status' => 'pending']);
        });

        return $url;
    }

    /**
     * expired_date DOKU format yyyyMMddHHmmss zona UTC+7. Konversi ke app tz.
     * // ponytail: konfirmasi timezone vs sandbox saat E2E.
     */
    private function parseExpiredDate(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }

        return Carbon::createFromFormat('YmdHis', $raw, 'Asia/Jakarta')
            ->setTimezone(config('app.timezone'))
            ->toDateTimeString();
    }
}
