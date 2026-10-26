<?php

namespace App\Http\Controllers\Doku;

use App\Actions\Member\MarkMemberPaymentPaid;
use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Services\Doku\DokuSignature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    public function __construct(
        private readonly DokuSignature $signature,
        private readonly MarkMemberPaymentPaid $markPaid,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $raw = $request->getContent();

        $valid = $this->signature->verify(
            (string) $request->header('Signature'),
            (string) $request->header('Request-Id'),
            (string) $request->header('Request-Timestamp'),
            '/webhooks/doku',
            $raw,
        );

        if (! $valid) {
            Log::warning('DOKU webhook signature invalid', ['request_id' => $request->header('Request-Id')]);

            return response()->json(['message' => 'invalid signature'], 401);
        }

        $payload = $request->json()->all();
        $invoice = (string) data_get($payload, 'order.invoice_number');
        $status = (string) data_get($payload, 'order.status', data_get($payload, 'transaction.status'));
        $amount = (int) data_get($payload, 'order.amount');

        $tx = PaymentTransaction::where('invoice_number', $invoice)->first();

        if (! $tx) {
            Log::warning('DOKU webhook: transaction tidak ditemukan', ['invoice' => $invoice]);

            return response()->json(['message' => 'ok'], 200);
        }

        if ($amount !== (int) $tx->amount) {
            Log::warning('DOKU webhook: amount mismatch', ['invoice' => $invoice, 'expected' => $tx->amount, 'got' => $amount]);

            return response()->json(['message' => 'amount mismatch'], 422);
        }

        $tx->update(['raw_notification' => $payload, 'channel' => data_get($payload, 'channel.id')]);

        if ($status === 'SUCCESS') {
            DB::transaction(function () use ($tx) {
                $tx->update(['status' => 'success', 'paid_at' => now()]);
                $this->markPaid->handle($tx->memberPayment, 'doku', $tx->invoice_number);
            });
        } elseif ($status === 'EXPIRED') {
            $tx->update(['status' => 'expired']);
            // buka retry: kembalikan member_payment ke unpaid jika belum paid
            $tx->memberPayment()->where('payment_status', 'pending')->update(['payment_status' => 'unpaid']);
        }
        // FAILED: diabaikan (best practice Checkout - biar customer ganti metode)

        return response()->json(['message' => 'ok'], 200);
    }
}
