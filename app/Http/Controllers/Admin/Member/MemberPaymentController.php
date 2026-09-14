<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\UpdateMemberPaymentRequest;
use App\Models\MemberAccount;
use App\Models\MemberPayment;
use App\Repositories\Member\MemberRegistrationRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MemberPaymentController extends Controller
{
    public function __construct(
        private readonly MemberRegistrationRepository $registrationRepo,
    ) {}

    public function update(UpdateMemberPaymentRequest $request, MemberPayment $memberPayment): RedirectResponse
    {
        $data = $request->validated();

        // Gap 153: Cicilan harus dibayar urut — cek cicilan sebelumnya sudah paid
        if (($data['payment_status'] ?? null) === 'paid' && $memberPayment->installment_number > 1) {
            $previousPaid = MemberPayment::where('member_registration_id', $memberPayment->member_registration_id)
                ->where('installment_number', $memberPayment->installment_number - 1)
                ->where('payment_status', 'paid')
                ->exists();

            if (! $previousPaid) {
                throw ValidationException::withMessages([
                    'payment_status' => "Cicilan ke-{$memberPayment->installment_number} belum bisa dikonfirmasi, cicilan sebelumnya belum dibayar.",
                ]);
            }
        }

        DB::transaction(function () use ($memberPayment, $data) {
            if (($data['payment_status'] ?? null) === 'paid' && ! $memberPayment->paid_at) {
                $data['paid_at'] = now();
            }

            $memberPayment->update($data);

            // Cicilan pertama dibayar → aktifkan akun member
            if ($memberPayment->installment_number === 1 && $memberPayment->payment_status === 'paid') {
                MemberAccount::where('members_data_id', $memberPayment->registration->members_data_id)
                    ->update(['is_active' => true]);
            }

            $this->registrationRepo->syncPaymentStatus($memberPayment->registration);
        });

        return redirect()->back()->with('success', 'Data pembayaran berhasil diperbarui.');
    }
}
