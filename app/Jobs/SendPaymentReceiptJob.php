<?php

namespace App\Jobs;

use App\Mail\MemberPaymentReceiptMail;
use App\Models\MemberPayment;
use App\Services\Letter\ConvertApiService;
use App\Services\Member\MemberReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 120;

    public function __construct(
        private readonly MemberPayment $payment,
    ) {}

    public function handle(MemberReceiptService $receiptService, ConvertApiService $convertApi): void
    {
        $email = $this->payment->registration->memberData?->account?->email
            ?? $this->payment->registration->memberData?->email;

        if (! $email) {
            return;
        }

        try {
            $docxPath = $receiptService->generate($this->payment->registration);
            $pdfBinary = $convertApi->docxToPdf($docxPath);
        } catch (\Throwable $e) {
            Log::error('SendPaymentReceiptJob: gagal generate kwitansi', [
                'payment_id' => $this->payment->id,
                'error' => $e->getMessage(),
            ]);

            return;
        } finally {
            if (isset($docxPath) && file_exists($docxPath)) {
                unlink($docxPath);
            }
        }

        if (! $pdfBinary) {
            Log::error('SendPaymentReceiptJob: ConvertApi tidak mengembalikan PDF', [
                'payment_id' => $this->payment->id,
            ]);

            return;
        }

        Mail::to($email)->send(new MemberPaymentReceiptMail($this->payment, $pdfBinary));
    }
}
