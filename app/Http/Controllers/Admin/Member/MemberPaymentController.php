<?php

namespace App\Http\Controllers\Admin\Member;

use App\Enums\Member\ActivationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\UpdateMemberPaymentRequest;
use App\Jobs\SendPaymentReceiptJob;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Repositories\Member\MemberRegistrationRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MemberPaymentController extends Controller
{
    public function __construct(
        private readonly MemberRegistrationRepository $registrationRepo,
    ) {}

    public function store(Request $request, MemberRegistration $registration): RedirectResponse
    {
        abort_unless($request->user()->can('member.payment.manage'), 403);

        $nextNumber = $registration->payments()->max('installment_number') + 1;

        MemberPayment::create([
            'member_registration_id' => $registration->id,
            'installment_number' => $nextNumber,
            'amount' => $request->integer('amount'),
            'payment_status' => 'unpaid',
        ]);

        return back()->with('success', 'Cicilan berhasil ditambahkan.');
    }

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

            $membersDataId = $memberPayment->registration->members_data_id;

            // Cicilan pertama dibayar → aktifkan login akun
            if ($memberPayment->installment_number === 1 && $memberPayment->payment_status === 'paid') {
                MemberAccount::where('members_data_id', $membersDataId)
                    ->update(['is_active' => true]);
            }

            // Pembayaran apapun dikonfirmasi → pulihkan akses jika ditangguhkan atau pending
            if ($memberPayment->payment_status === 'paid') {
                MemberData::where('id', $membersDataId)
                    ->whereIn('activation_status', [
                        ActivationStatus::Pending->value,
                        ActivationStatus::Ditangguhkan->value,
                    ])
                    ->update(['activation_status' => ActivationStatus::Aktif->value]);
            }

            $this->registrationRepo->syncPaymentStatus($memberPayment->registration);
        });

        if (($data['payment_status'] ?? null) === 'paid') {
            dispatch(new SendPaymentReceiptJob($memberPayment));
        }

        return redirect()->back()->with('success', 'Data pembayaran berhasil diperbarui.');
    }
}
