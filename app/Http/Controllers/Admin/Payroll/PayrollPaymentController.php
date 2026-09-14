<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\MarkPaymentFailedRequest;
use App\Http\Requests\Payroll\ProcessPaymentRequest;
use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollPayment;
use App\Models\PayrollPeriod;
use App\Repositories\Payroll\EmployeePayrollRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollPaymentController extends Controller
{
    public function __construct(
        private readonly EmployeePayrollRepository $payrollRepo,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('payroll.period.pay'), 403);

        $paymentStatus = $request->string('status')->value();
        $search = $request->string('search')->trim()->value();

        $payrolls = EmployeePayroll::query()
            ->with(['period', 'payments'])
            ->whereHas('period', fn ($query) => $query->where('status', 'finalized'))
            ->when($paymentStatus, fn ($query) => $query->where('payment_status', $paymentStatus))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('employee_name_snapshot', 'like', "%{$search}%")
                        ->orWhere('employee_code_snapshot', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.payroll.payments.index', compact('payrolls'));
    }

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
            'paid_by_employee_id' => auth()->user()->employee?->id,
        ]));

        $this->payrollRepo->syncPaymentStatus($payroll);

        return redirect()->route('payroll.payments.index')->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function markFailed(MarkPaymentFailedRequest $request, PayrollPeriod $period, EmployeePayroll $payroll, EmployeePayrollPayment $payment): RedirectResponse
    {
        $payment->update([
            'payment_status' => 'failed',
            'failure_reason' => $request->validated('failure_reason'),
        ]);

        return redirect()->route('payroll.payments.index')->with('success', 'Status pembayaran diperbarui.');
    }
}
