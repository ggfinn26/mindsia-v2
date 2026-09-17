<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeePayroll;
use App\Services\TelegramStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PayslipController extends Controller
{
    public function __construct(
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function index(): View
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403);

        $payrolls = EmployeePayroll::with(['period', 'slip'])
            ->where('employee_id', $employee->id)
            ->whereHas('period', fn ($q) => $q->where('status', 'finalized'))
            ->whereHas('slip')
            ->orderByDesc('id')
            ->get();

        return view('employee.payslip.index', compact('payrolls'));
    }

    public function preview(EmployeePayroll $payroll): Response|RedirectResponse
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403);
        abort_if($payroll->employee_id !== $employee->id, 403);
        abort_unless($payroll->period?->isFinalized(), 403);

        $slip = $payroll->slip;

        if (! $slip?->telegram_file_id) {
            return back()->with('error', 'Slip belum tersedia.');
        }

        $content = $this->telegramStorage->downloadFile($slip->telegram_file_id);

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"slip_{$payroll->employee_code_snapshot}.pdf\"",
        ]);
    }

    public function share(EmployeePayroll $payroll): JsonResponse|RedirectResponse
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403);
        abort_if($payroll->employee_id !== $employee->id, 403);
        abort_unless($payroll->period?->isFinalized(), 403);

        $slip = $payroll->slip;
        abort_unless($slip?->telegram_file_id, 404, 'Slip belum tersedia.');

        $downloadUrl = route('employee.payslips.download', $payroll);
        $period = "{$payroll->period->period_month}/{$payroll->period->period_year}";

        if ($employee->email) {
            Mail::raw(
                "Slip gaji Anda untuk periode {$period} telah tersedia. Download: {$downloadUrl}",
                fn ($m) => $m->to($employee->email)->subject("Slip Gaji {$period}")
            );
        }

        $waNumber = $employee->whatsapp_number ?? $employee->phone_number ?? null;
        $waLink = $waNumber
            ? 'https://wa.me/'.preg_replace('/\D/', '', $waNumber).'?text='.urlencode("Slip gaji saya periode {$period}: {$downloadUrl}")
            : null;

        return response()->json([
            'email_sent' => (bool) $employee->email,
            'wa_link' => $waLink,
        ]);
    }

    public function download(EmployeePayroll $payroll): Response|RedirectResponse
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403);

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
