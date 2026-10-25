<?php

namespace App\Http\Controllers\Admin\Member;

use App\Enums\Member\ActivationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\UpdateMemberPaymentRequest;
use App\Jobs\SendPaymentReceiptJob;
use App\Models\Employee;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Models\ProspectiveMember;
use App\Repositories\Member\MemberRegistrationRepository;
use App\Services\Marketing\MarketingSnapshotService;
use App\Services\Member\MemberRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MemberPaymentController extends Controller
{
    public function __construct(
        private readonly MemberRegistrationRepository $registrationRepo,
        private readonly MarketingSnapshotService $snapshotService,
        private readonly MemberRegistrationService $registrationService,
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

            $registration = $memberPayment->registration;
            $membersDataId = $registration->members_data_id;

            $this->registrationRepo->syncPaymentStatus($registration);
            $registration->refresh();

            if (($data['payment_status'] ?? null) === 'paid') {
                $this->registrationService->consumePromoQuota($registration);
            }

            if ($registration->payment_status !== 'unpaid') {
                MemberAccount::where('members_data_id', $membersDataId)
                    ->update(['is_active' => true]);

                MemberData::where('id', $membersDataId)
                    ->update(['activation_status' => ActivationStatus::Aktif->value]);

                $member = MemberData::find($membersDataId);
                if ($member) {
                    ProspectiveMember::where('status', '!=', ProspectiveMember::STATUS_FIXED)
                        ->where(function ($q) use ($member) {
                            $q->when($member->whatsapp_number, fn ($q) => $q->orWhere('whatsapp_number', $member->whatsapp_number))
                                ->when($member->email, fn ($q) => $q->orWhere('email', $member->email));
                        })
                        ->update(['status' => ProspectiveMember::STATUS_FIXED]);
                }
            }
        });

        if (($data['payment_status'] ?? null) === 'paid') {
            dispatch(new SendPaymentReceiptJob($memberPayment));

            $employeeId = $memberPayment->registration->memberData?->referred_by_employee_id;
            $paidAt = $memberPayment->fresh()->paid_at ?? now();
            if ($employeeId) {
                $employee = Employee::with('branch.area.region')->find($employeeId);
                if ($employee) {
                    $this->snapshotService->append($employee, (int) $paidAt->month, (int) $paidAt->year);
                }
            }
        }

        return redirect()->back()->with('success', 'Data pembayaran berhasil diperbarui.');
    }
}
