<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\GenerateSlipRequest;
use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use App\Services\Payroll\PayrollNotificationService;
use App\Services\Payroll\PayrollSlipService;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class PayrollSlipController extends Controller
{
    public function __construct(
        private readonly PayrollSlipService $slipService,
        private readonly PayrollNotificationService $notificationService,
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function generate(GenerateSlipRequest $request, PayrollPeriod $period, EmployeePayroll $payroll): RedirectResponse
    {
        $generatedBy = auth()->user()->employee;
        $slip = $this->slipService->generate($payroll, $generatedBy);
        $this->notificationService->notifySlipReady($payroll);

        return redirect()->route('payroll.periods.payroll.show', [$period, $payroll])->with('success', 'Slip gaji berhasil digenerate.');
    }

    public function download(PayrollPeriod $period, EmployeePayroll $payroll): Response|RedirectResponse
    {
        $slip = $payroll->slip;

        if (! $slip?->telegram_file_id) {
            return back()->with('error', 'Slip belum digenerate.');
        }

        $content = $this->telegramStorage->downloadFile($slip->telegram_file_id);

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"slip_{$payroll->employee_code_snapshot}_{$period->period_year}_{$period->period_month}.txt\"",
        ]);
    }
}
