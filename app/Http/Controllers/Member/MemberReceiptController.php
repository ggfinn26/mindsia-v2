<?php

namespace App\Http\Controllers\Member;

use App\Actions\Member\GenerateAndStoreReceipt;
use App\Http\Controllers\Controller;
use App\Models\MemberPayment;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class MemberReceiptController extends Controller
{
    public function download(MemberPayment $memberPayment, TelegramStorageService $storage): Response
    {
        $this->authorizeOwnership($memberPayment);

        abort_if($memberPayment->telegram_receipt_id === null, 404, 'Kwitansi belum tersedia.');

        $binary = $storage->downloadFile($memberPayment->telegram_receipt_id);

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="kwitansi-'.$memberPayment->id.'.pdf"',
        ]);
    }

    public function generate(MemberPayment $memberPayment, GenerateAndStoreReceipt $generateReceipt): RedirectResponse
    {
        $this->authorizeOwnership($memberPayment);

        abort_unless($memberPayment->payment_status === 'paid', 422, 'Kwitansi hanya untuk pembayaran lunas.');

        $pdfBinary = $generateReceipt->handle($memberPayment);

        if (! $pdfBinary) {
            return back()->with('error', 'Gagal membuat kwitansi - kuota konversi habis. Coba lagi nanti.');
        }

        return back()->with('success', 'Kwitansi berhasil dibuat. Silakan unduh.');
    }

    private function authorizeOwnership(MemberPayment $payment): void
    {
        $membersDataId = auth('member')->user()?->memberData?->id;

        abort_unless(
            $membersDataId !== null && $payment->registration->members_data_id === $membersDataId,
            403,
        );
    }
}
