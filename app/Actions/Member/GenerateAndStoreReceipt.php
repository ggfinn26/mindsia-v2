<?php

namespace App\Actions\Member;

use App\Models\MemberPayment;
use App\Services\Letter\ConvertApiService;
use App\Services\Member\MemberReceiptService;
use App\Services\TelegramStorageService;
use Illuminate\Support\Facades\Log;

class GenerateAndStoreReceipt
{
    public function __construct(
        private readonly MemberReceiptService $receiptService,
        private readonly ConvertApiService $convertApi,
        private readonly TelegramStorageService $storage,
    ) {}

    /**
     * Generate the receipt PDF, archive it (local + Telegram backup), and persist
     * the storage reference on the payment. Returns the PDF binary so the caller
     * can also email it, or null when conversion failed (e.g. ConvertApi quota).
     * Always regenerates and overwrites the stored reference.
     */
    public function handle(MemberPayment $payment): ?string
    {
        $docxPath = null;

        try {
            $docxPath = $this->receiptService->generate($payment->registration);
            $pdfBinary = $this->convertApi->docxToPdf($docxPath);
        } catch (\Throwable $e) {
            Log::error('GenerateAndStoreReceipt: gagal generate kwitansi', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        } finally {
            if ($docxPath && file_exists($docxPath)) {
                unlink($docxPath);
            }
        }

        if (! $pdfBinary) {
            return null;
        }

        $tmp = tempnam(sys_get_temp_dir(), 'receipt');
        file_put_contents($tmp, $pdfBinary);

        try {
            $stored = $this->storage->uploadFile($tmp, 'kwitansi-'.$payment->id.'.pdf', 'receipt', $payment->id);
            $payment->update(['telegram_receipt_id' => $stored['file_id']]);
        } finally {
            if (file_exists($tmp)) {
                unlink($tmp);
            }
        }

        return $pdfBinary;
    }
}
