<?php

namespace App\Repositories\Payroll;

use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollAdjustmentHistory;
use App\Models\EmployeePayrollSlip;
use App\Models\PayrollBonusCalculation;
use App\Models\PayrollBonusConditionSnapshot;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Collection;

class EmployeePayrollRepository
{
    public function allByPeriod(PayrollPeriod $period): Collection
    {
        return EmployeePayroll::with(['employee', 'items', 'payments', 'slip'])
            ->where('payroll_period_id', $period->id)
            ->get();
    }

    public function findById(int $id): EmployeePayroll
    {
        return EmployeePayroll::with(['employee', 'items.bonusCalculation', 'payments', 'slip', 'adjustmentHistories.adjustedBy'])
            ->findOrFail($id);
    }

    public function findByPeriodAndEmployee(int $periodId, int $employeeId): ?EmployeePayroll
    {
        return EmployeePayroll::where('payroll_period_id', $periodId)
            ->where('employee_id', $employeeId)
            ->first();
    }

    public function create(array $data): EmployeePayroll
    {
        return EmployeePayroll::create($data);
    }

    public function updateTotals(EmployeePayroll $payroll): EmployeePayroll
    {
        $items = $payroll->items()->get();

        $totalEarnings = $items->where('component_type_snapshot', 'earning')->sum('total_amount');
        $totalDeductions = $items->where('component_type_snapshot', 'deduction')->sum('total_amount');

        $payroll->update([
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_amount' => $totalEarnings - $totalDeductions,
        ]);

        return $payroll->fresh();
    }

    public function deleteByPeriod(PayrollPeriod $period): void
    {
        $payrollIds = EmployeePayroll::where('payroll_period_id', $period->id)->pluck('id');

        PayrollBonusConditionSnapshot::whereHas(
            'bonusCalculation',
            fn ($q) => $q->whereIn('employee_payroll_id', $payrollIds)
        )->delete();

        PayrollBonusCalculation::whereIn('employee_payroll_id', $payrollIds)->delete();
        PayrollItem::whereIn('employee_payroll_id', $payrollIds)->delete();
        EmployeePayrollSlip::whereIn('employee_payroll_id', $payrollIds)->delete();
        EmployeePayrollAdjustmentHistory::whereIn('employee_payroll_id', $payrollIds)->delete();

        EmployeePayroll::whereIn('id', $payrollIds)->delete();
    }

    public function updatePaymentStatus(EmployeePayroll $payroll, string $status): EmployeePayroll
    {
        $update = ['payment_status' => $status];

        if ($status === 'paid') {
            $update['paid_at'] = now();
        }

        $payroll->update($update);

        return $payroll;
    }

    public function syncPaymentStatus(EmployeePayroll $payroll): void
    {
        $totalPaid = $payroll->payments()->where('payment_status', 'paid')->sum('amount');

        if ($totalPaid >= (float) $payroll->net_amount) {
            $this->updatePaymentStatus($payroll, 'paid');
        } elseif ($totalPaid > 0) {
            $this->updatePaymentStatus($payroll, 'partial');
        }
    }
}
