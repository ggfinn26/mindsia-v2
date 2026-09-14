<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\AdjustPayrollItemRequest;
use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollAdjustmentHistory;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Repositories\Payroll\EmployeePayrollRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeePayrollController extends Controller
{
    public function __construct(
        private readonly EmployeePayrollRepository $repo,
    ) {}

    public function show(PayrollPeriod $period, EmployeePayroll $payroll): View
    {
        return view('admin.payroll.employee-payroll.show', [
            'period' => $period,
            'payroll' => $this->repo->findById($payroll->id),
        ]);
    }

    public function adjust(AdjustPayrollItemRequest $request, PayrollPeriod $period, EmployeePayroll $payroll): RedirectResponse
    {
        if (! $period->isFinalized()) {
            return back()->with('error', 'Adjustment hanya bisa dilakukan saat periode sudah finalized.');
        }

        $itemId = $request->input('payroll_item_id');
        $item = $itemId ? PayrollItem::findOrFail($itemId) : null;

        if ($item && $item->employee_payroll_id !== $payroll->id) {
            abort(403, 'Item payroll bukan milik karyawan ini.');
        }

        $previousAmount = $item ? (float) $item->total_amount : (float) $payroll->net_amount;
        $newAmount = (float) $request->input('new_amount');

        EmployeePayrollAdjustmentHistory::create([
            'employee_payroll_id' => $payroll->id,
            'payroll_item_id' => $itemId,
            'adjustment_type' => $request->input('adjustment_type'),
            'previous_amount' => $previousAmount,
            'new_amount' => $newAmount,
            'adjustment_reason' => $request->input('adjustment_reason'),
            'adjusted_by_employee_id' => auth()->user()->employee?->id,
        ]);

        if ($item) {
            $item->update(['total_amount' => $newAmount]);
            $this->repo->updateTotals($payroll);
        } else {
            // correction type: directly set net_amount, bypassing item recalc
            $payroll->update(['net_amount' => $newAmount]);
        }

        return redirect()->route('payroll.payrolls.show', [$period, $payroll])->with('success', 'Adjustment berhasil disimpan.');
    }
}
