<?php

namespace App\Console\Commands;

use App\Actions\Member\MarkMemberPaymentPaid;
use App\Models\PaymentTransaction;
use App\Services\Doku\DokuSignature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuSyncPending extends Command
{
    protected $signature = 'doku:sync-pending';

    protected $description = 'Poll check-status DOKU untuk transaksi pending yang kedaluwarsa (fallback webhook miss).';

    public function handle(DokuSignature $signature, MarkMemberPaymentPaid $markPaid): int
    {
        $pending = PaymentTransaction::where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '<', now());
            })->get();

        foreach ($pending as $tx) {
            $target = '/orders/v1/status/'.$tx->invoice_number;
            $requestId = (string) Str::uuid();
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');

            $response = Http::withHeaders([
                'Client-Id' => $signature->clientId(),
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $signature->sign($requestId, $timestamp, $target, null),
            ])->get(rtrim((string) config('services.doku.base_url'), '/').$target);

            if ($response->failed()) {
                $this->warn("Gagal cek {$tx->invoice_number}: {$response->status()}");

                continue;
            }

            $status = (string) data_get($response->json(), 'transaction.status');

            if ($status === 'SUCCESS') {
                $tx->update(['status' => 'success', 'paid_at' => now(), 'raw_notification' => $response->json()]);
                $markPaid->handle($tx->memberPayment, 'doku', $tx->invoice_number);
                $this->info("{$tx->invoice_number}: SUCCESS");
            } elseif (in_array($status, ['EXPIRED', 'FAILED'], true)) {
                $tx->update(['status' => $status === 'EXPIRED' ? 'expired' : 'failed']);
                $tx->memberPayment()->where('payment_status', 'pending')->update(['payment_status' => 'unpaid']);
                $this->info("{$tx->invoice_number}: {$status}");
            }
        }

        return self::SUCCESS;
    }
}
