<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\ProcessPaymentRequest;
use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollPayment;
use App\Models\PayrollPeriod;
use App\Repositories\Payroll\EmployeePayrollRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PayrollPaymentController extends Controller
{
    public function __construct(
        private readonly EmployeePayrollRepository $payrollRepo,
    ) {}

    public function store(ProcessPaymentRequest $request, PayrollPeriod $period, EmployeePayroll $payroll): RedirectResponse
    {
        if (! $period->isFinalized()) {
            return back()->with('error', 'Pembayaran hanya bisa diproses setelah periode finalized.');
        }

        $alreadyPaid = $payroll->payments()->where('payment_status', 'paid')->sum('amount');
        $remaining = (float) $payroll->net_amount - $alreadyPaid;

        if ((float) $request->input('amount') > $remaining + 0.01) {
            return back()->with('error', 'Jumlah pembayaran melebihi sisa tagihan (Rp '.number_format($remaining, 0, ',', '.').').');
        }

        EmployeePayrollPayment::create(array_merge($request->validated(), [
            'employee_payroll_id' => $payroll->id,
            'payment_status' => 'paid',
            'paid_by_employee_id' => auth()->user()->employee->id,
        ]));

        $this->payrollRepo->syncPaymentStatus($payroll);

        return redirect()->route('payroll.periods.payroll.show', [$period, $payroll])->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function markFailed(Request $request, PayrollPeriod $period, EmployeePayroll $payroll, EmployeePayrollPayment $payment): RedirectResponse
    {
        $payment->update([
            'payment_status' => 'failed',
            'failure_reason' => $request->input('failure_reason'),
        ]);

        return redirect()->route('payroll.periods.payroll.show', [$period, $payroll])->with('success', 'Status pembayaran diperbarui.');
    }
}
