<?php

namespace App\Repositories\Member;

use App\Models\MemberRegistration;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MemberRegistrationRepository
{
    public function findWithDetails(int $id): MemberRegistration
    {
        return MemberRegistration::with(['memberData.account', 'program', 'paymentPlan', 'employee', 'discount', 'payments', 'review'])
            ->findOrFail($id);
    }

    public function paginateForMember(int $memberId): LengthAwarePaginator
    {
        return MemberRegistration::with(['program', 'payments'])
            ->where('members_data_id', $memberId)
            ->orderByDesc('created_at')
            ->paginate(25);
    }

    public function updateGraduation(MemberRegistration $registration, string $status): MemberRegistration
    {
        $registration->update(['graduation_status' => $status]);

        return $registration;
    }

    public function syncPaymentStatus(MemberRegistration $registration): void
    {
        $payments = $registration->payments;
        $paidCount = $payments->where('payment_status', 'paid')->count();
        $expectedCount = $registration->paymentPlan?->installment_count ?? 1;

        $status = match (true) {
            $paidCount >= $expectedCount && $paidCount > 0 => 'paid_full',
            $paidCount > 0 => 'installments',
            default => 'unpaid',
        };

        $registration->update(['payment_status' => $status]);
    }
}
