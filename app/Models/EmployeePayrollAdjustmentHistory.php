<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayrollAdjustmentHistory extends Model
{
    protected $fillable = [
        'employee_payroll_id',
        'payroll_item_id',
        'adjustment_type',
        'previous_amount',
        'new_amount',
        'adjustment_reason',
        'adjusted_by_employee_id',
    ];

    protected $casts = [
        'previous_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
    ];

    public function employeePayroll(): BelongsTo
    {
        return $this->belongsTo(EmployeePayroll::class);
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function adjustedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'adjusted_by_employee_id');
    }
}
