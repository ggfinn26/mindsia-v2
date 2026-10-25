<?php

namespace App\Console\Commands;

use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Models\ProgramPaymentPlan;
use App\Services\Member\MemberRegistrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResyncMemberInstallments extends Command
{
    protected $signature = 'member:resync-installments';

    protected $description = 'Regenerate cicilan unpaid registrasi berdiskon agar total == final_price (lewati yang sudah ada cicilan paid).';

    public function handle(MemberRegistrationService $service): int
    {
        $registrations = MemberRegistration::where('discount_amount', '>', 0)->get();

        foreach ($registrations as $registration) {
            if ($registration->payments()->where('payment_status', 'paid')->exists()) {
                $this->warn("Lewati registrasi {$registration->id}: ada cicilan sudah paid.");

                continue;
            }

            $plan = ProgramPaymentPlan::find($registration->payment_plan_id);

            if (! $plan) {
                $this->warn("Lewati registrasi {$registration->id}: payment plan tidak ditemukan.");

                continue;
            }

            DB::transaction(function () use ($registration, $plan, $service) {
                $now = now();
                $registration->payments()->delete();

                $rows = [];
                foreach ($service->installmentAmounts($registration, $plan) as $index => $amount) {
                    $rows[] = [
                        'member_registration_id' => $registration->id,
                        'installment_number' => $index + 1,
                        'amount' => $amount,
                        'payment_status' => 'unpaid',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                MemberPayment::insert($rows);
            });

            $this->info("Registrasi {$registration->id} di-resync.");
        }

        return self::SUCCESS;
    }
}
