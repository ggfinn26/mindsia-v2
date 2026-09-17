<?php

namespace App\Jobs;

use App\Models\EmployeePayroll;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPayrollSlipNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        private readonly EmployeePayroll $payroll,
    ) {}

    public function handle(): void
    {
        $employee = $this->payroll->employee;

        if (! $employee?->email) {
            return;
        }

        $period = $this->payroll->period;

        Mail::raw(
            "Slip gaji Anda untuk periode {$period->period_month}/{$period->period_year} telah tersedia. Silakan login untuk melihat slip gaji Anda.",
            fn ($msg) => $msg->to($employee->email)->subject('Slip Gaji Tersedia')
        );
    }
}
