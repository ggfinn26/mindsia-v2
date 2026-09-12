<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmployeePayroll extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'employee_code_snapshot',
        'employee_name_snapshot',
        'position_name_snapshot',
        'branch_name_snapshot',
        'scheduled_working_days',
        'effective_working_days',
        'days_present',
        'days_absent',
        'days_sick',
        'days_permission',
        'days_leave',
        'days_holiday',
        'days_late',
        'total_sessions',
        'attended_sessions',
        'absent_sessions',
        'late_sessions',
        'total_earnings',
        'total_deductions',
        'net_amount',
        'payment_status',
        'paid_at',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'total_earnings' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(EmployeePayrollPayment::class);
    }

    public function slip(): HasOne
    {
        return $this->hasOne(EmployeePayrollSlip::class);
    }

    public function adjustmentHistories(): HasMany
    {
        return $this->hasMany(EmployeePayrollAdjustmentHistory::class);
    }
}
