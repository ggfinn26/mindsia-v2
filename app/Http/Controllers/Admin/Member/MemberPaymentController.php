<?php

namespace App\Http\Controllers\Admin\Member;

use App\Actions\Member\MarkMemberPaymentPaid;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\UpdateMemberPaymentRequest;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Repositories\Member\MemberRegistrationRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

        if (($data['payment_status'] ?? null) === 'paid') {
            // Persist field lain (bukti, catatan, metode) dulu; transisi -> paid + side effect lewat action.
            $extra = Arr::except($data, ['payment_status', 'paid_at']);
            if ($extra !== []) {
                $memberPayment->update($extra);
            }

            $method = $data['payment_method'] ?? $memberPayment->payment_method ?? 'manual';
            app(MarkMemberPaymentPaid::class)->handle($memberPayment->fresh(), $method, null);
        } else {
            $memberPayment->update($data);
            $this->registrationRepo->syncPaymentStatus($memberPayment->registration);
        }

        return redirect()->back()->with('success', 'Data pembayaran berhasil diperbarui.');
    }
}
