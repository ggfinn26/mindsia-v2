<?php

namespace App\Services\Member;

use App\Models\Discount;
use App\Models\MemberData;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use Illuminate\Support\Facades\DB;

class MemberRegistrationService
{
    public function register(MemberData $member, array $data): MemberRegistration
    {
        return DB::transaction(function () use ($member, $data) {
            $discount = null;
            $discountAmount = 0;

            if (! empty($data['discount_code'])) {
                $discount = Discount::where('discount_code', $data['discount_code'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->validateDiscount($discount, $data['program_id']);

                $discountAmount = $this->calculateDiscount($discount, $data['original_price']);

                if ($discount->discount_quota !== null) {
                    $discount->decrement('discount_quota');
                }
            }

            $registration = MemberRegistration::create([
                'members_data_id' => $member->id,
                'program_id' => $data['program_id'],
                'employee_id' => $data['employee_id'],
                'receipt_member_name' => $member->full_name,
                'receipt_institution_name' => $member->institution?->institution_name ?? '',
                'receipt_program_name' => $data['receipt_program_name'],
                'original_price' => $data['original_price'],
                'discount_id' => $discount?->id,
                'discount_code' => $discount?->discount_code,
                'discount_amount' => $discountAmount,
                'final_price' => $data['original_price'] - $discountAmount,
                'installment_type' => $data['installment_type'],
                'graduation_status' => 'BELUM_LULUS',
                'payment_status' => 'unpaid',
            ]);

            $this->generateInstallments($registration);

            return $registration;
        });
    }

    private function validateDiscount(Discount $discount, int $programId): void
    {
        if (! $discount->isValidForProgram($programId)) {
            throw new \RuntimeException('Diskon tidak berlaku untuk program ini.');
        }

        if ($discount->discount_quota !== null && $discount->discount_quota <= 0) {
            throw new \RuntimeException('Kuota diskon sudah habis.');
        }

        if ($discount->expired_at && $discount->expired_at->isPast()) {
            throw new \RuntimeException('Diskon sudah kedaluwarsa.');
        }
    }

    private function calculateDiscount(Discount $discount, int $originalPrice): int
    {
        if ($discount->discount_type === 'percentage') {
            return (int) round($originalPrice * $discount->discount_nominal / 100);
        }

        return min($discount->discount_nominal, $originalPrice);
    }

    private function generateInstallments(MemberRegistration $registration): void
    {
        $count = (int) $registration->installment_type;
        $base = intdiv($registration->final_price, $count);
        $remainder = $registration->final_price % $count;

        $now = now();
        $installments = [];
        for ($i = 1; $i <= $count; $i++) {
            $installments[] = [
                'member_registration_id' => $registration->id,
                'installment_number' => $i,
                'amount' => $base + ($i === 1 ? $remainder : 0),
                'payment_status' => 'unpaid',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        MemberPayment::insert($installments);
    }
}
