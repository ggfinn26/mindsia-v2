<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\UpdateMemberPaymentRequest;
use App\Models\MemberAccount;
use App\Models\MemberPayment;
use App\Repositories\Member\MemberRegistrationRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MemberPaymentController extends Controller
{
    public function __construct(
        private readonly MemberRegistrationRepository $registrationRepo,
    ) {}

    public function update(UpdateMemberPaymentRequest $request, MemberPayment $memberPayment): RedirectResponse
    {
        $data = $request->validated();

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
