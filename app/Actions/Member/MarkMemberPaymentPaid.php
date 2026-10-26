<?php

namespace App\Actions\Member;

use App\Enums\Member\ActivationStatus;
use App\Jobs\SendPaymentReceiptJob;
use App\Models\Employee;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\MemberPayment;
use App\Models\ProspectiveMember;
use App\Repositories\Member\MemberRegistrationRepository;
use App\Services\Marketing\MarketingSnapshotService;
use App\Services\Member\MemberRegistrationService;
use Illuminate\Support\Facades\DB;

class MarkMemberPaymentPaid
{
    public function __construct(
        private readonly MemberRegistrationRepository $registrationRepo,
        private readonly MarketingSnapshotService $snapshotService,
        private readonly MemberRegistrationService $registrationService,
    ) {}

    /**
     * Idempotent. Return true jika transisi unpaid/pending -> paid terjadi; false jika sudah paid.
     */
    public function handle(MemberPayment $payment, string $method, ?string $reference = null): bool
    {
        $changed = DB::transaction(function () use ($payment, $method, $reference) {
            $locked = MemberPayment::whereKey($payment->id)->lockForUpdate()->first();

            if (! $locked || $locked->payment_status === 'paid') {
                return false;
            }

            $locked->update([
                'payment_status' => 'paid',
                'payment_method' => $method,
                'paid_at' => now(),
                'notes' => $reference ? trim(($locked->notes ? $locked->notes.' | ' : '').'ref:'.$reference) : $locked->notes,
            ]);

            $registration = $locked->registration;
            $membersDataId = $registration->members_data_id;

            $this->registrationRepo->syncPaymentStatus($registration);
            $registration->refresh();

            $this->registrationService->consumePromoQuota($registration);

            if ($registration->payment_status !== 'unpaid') {
                MemberAccount::where('members_data_id', $membersDataId)->update(['is_active' => true]);
                MemberData::where('id', $membersDataId)->update(['activation_status' => ActivationStatus::Aktif->value]);

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

            return true;
        });

        if ($changed) {
            $fresh = $payment->fresh();
            dispatch(new SendPaymentReceiptJob($fresh));

            $employeeId = $fresh->registration->memberData?->referred_by_employee_id;
            if ($employeeId) {
                $paidAt = $fresh->paid_at ?? now();
                $employee = Employee::with('branch.area.region')->find($employeeId);
                if ($employee) {
                    $this->snapshotService->append($employee, (int) $paidAt->month, (int) $paidAt->year);
                }
            }
        }

        return $changed;
    }
}
