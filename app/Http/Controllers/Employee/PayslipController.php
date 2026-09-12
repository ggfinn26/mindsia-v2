<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeePayroll;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PayslipController extends Controller
{
    public function __construct(
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function index(): View
    {
        $employee = auth()->user()->employee;

        $payrolls = EmployeePayroll::with(['period', 'slip'])
            ->where('employee_id', $employee->id)
            ->whereHas('period', fn ($q) => $q->where('status', 'finalized'))
            ->whereHas('slip')
            ->orderByDesc('id')
            ->get();

        return view('employee.payslip.index', compact('payrolls'));
    }

    public function download(EmployeePayroll $payroll): Response|RedirectResponse
    {
        $employee = auth()->user()->employee;

        abort_if($payroll->employee_id !== $employee->id, 403);
        abort_unless($payroll->period?->isFinalized(), 403);

        $slip = $payroll->slip;

        if (! $slip?->telegram_file_id) {
            return back()->with('error', 'Slip belum tersedia.');
        }

        $content = $this->telegramStorage->downloadFile($slip->telegram_file_id);

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"slip_{$payroll->employee_code_snapshot}.txt\"",
        ]);
    }
}
