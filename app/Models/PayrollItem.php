<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollItem extends Model
{
    protected $fillable = [
        'employee_payroll_id',
        'payroll_component_id',
        'component_code_snapshot',
        'component_name_snapshot',
        'component_type_snapshot',
        'quantity',
        'unit_value',
        'total_amount',
        'source_type',
        'source_id',
        'description',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_value' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function employeePayroll(): BelongsTo
    {
        return $this->belongsTo(EmployeePayroll::class);
    }

    public function payrollComponent(): BelongsTo
    {
        return $this->belongsTo(PayrollComponent::class);
    }

    public function bonusCalculation(): HasOne
    {
        return $this->hasOne(PayrollBonusCalculation::class);
    }
}
