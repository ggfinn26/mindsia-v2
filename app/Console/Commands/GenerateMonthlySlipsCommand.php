<?php

namespace App\Console\Commands;

use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use App\Services\Payroll\PayrollNotificationService;
use App\Services\Payroll\PayrollSlipService;
use Illuminate\Console\Command;

class GenerateMonthlySlipsCommand extends Command
{
    protected $signature = 'payroll:generate-monthly-slips';

    protected $description = 'Generate slip gaji untuk semua employee pada periode finalized hari ini (tanggal 3)';

    public function __construct(
        private readonly PayrollSlipService $slipService,
        private readonly PayrollNotificationService $notificationService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $period = PayrollPeriod::where('status', 'finalized')
            ->whereDate('pay_date', today())
            ->first();

        if (! $period) {
            $this->info('Tidak ada periode finalized dengan pay_date hari ini. Skip.');

            return;
        }

        $payrolls = EmployeePayroll::where('payroll_period_id', $period->id)
            ->with(['employee', 'period', 'items'])
            ->get();

        $count = 0;
        foreach ($payrolls as $payroll) {
            $this->slipService->generate($payroll);
            $this->notificationService->notifySlipReady($payroll);
            $count++;
        }

        $this->info("Generate slip selesai: {$count} slip untuk periode {$period->period_month}/{$period->period_year}.");
    }
}
