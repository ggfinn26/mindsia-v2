<?php

namespace App\Jobs;

use App\Actions\Member\GenerateAndStoreReceipt;
use App\Mail\MemberPaymentReceiptMail;
use App\Models\MemberPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaymentReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 120;

    public function __construct(
        private readonly MemberPayment $payment,
    ) {}

    public function handle(GenerateAndStoreReceipt $generateReceipt): void
    {
        $email = $this->payment->registration->memberData?->account?->email
            ?? $this->payment->registration->memberData?->email;

        // Tetap generate + arsip kwitansi walau email kosong, agar bisa diunduh dari riwayat.
        $pdfBinary = $generateReceipt->handle($this->payment);

        if ($pdfBinary && $email) {
            Mail::to($email)->send(new MemberPaymentReceiptMail($this->payment, $pdfBinary));
        }
    }
}
