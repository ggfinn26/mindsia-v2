<?php

namespace App\Services\Payroll;

use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\Mail;

class PayrollNotificationService
{
    public function notifySlipReady(EmployeePayroll $payroll): void
    {
        $employee = $payroll->employee;

        if (! $employee?->email) {
            return;
        }

        // ponytail: in-app notification deferred to notification domain
        Mail::raw(
            "Slip gaji Anda untuk periode {$payroll->period->period_month}/{$payroll->period->period_year} telah tersedia. Silakan login untuk melihat slip gaji Anda.",
            fn ($msg) => $msg->to($employee->email)->subject('Slip Gaji Tersedia')
        );
    }

    public function notifyPayrollReady(PayrollPeriod $period): void
    {
        // ponytail: batch notify BOARD + HRR — deferred to notification domain
    }
}
