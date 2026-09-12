<?php

namespace App\Repositories\Member;

use App\Models\MemberRegistration;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MemberRegistrationRepository
{
    public function findWithDetails(int $id): MemberRegistration
    {
        return MemberRegistration::with(['memberData', 'program', 'employee', 'discount', 'payments', 'review'])
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
        $allPaid = $payments->every(fn ($p) => $p->payment_status === 'paid');
        $anyPaid = $payments->contains(fn ($p) => $p->payment_status === 'paid');

        $status = match (true) {
            $allPaid => 'paid_full',
            $anyPaid => 'installments',
            default => 'unpaid',
        };

        $registration->update(['payment_status' => $status]);
    }
}
