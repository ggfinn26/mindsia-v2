<?php

namespace App\Services\Payroll;

use App\Jobs\SendPayrollSlipNotificationJob;
use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;

class PayrollNotificationService
{
    public function notifySlipReady(EmployeePayroll $payroll): void
    {
        // ponytail: in-app notification deferred to notification domain
        SendPayrollSlipNotificationJob::dispatch($payroll);
    }

    public function notifyPayrollReady(PayrollPeriod $period): void
    {
        // ponytail: batch notify BOARD + HRR — deferred to notification domain
    }
}
