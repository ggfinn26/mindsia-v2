<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollBonusCalculation extends Model
{
    protected $fillable = [
        'employee_payroll_id',
        'payroll_item_id',
        'calculation_key',
        'bonus_type',
        'rule_id',
        'rule_code_snapshot',
        'rule_name_snapshot',
        'reward_type_snapshot',
        'reward_basis_snapshot',
        'reward_value_snapshot',
        'base_amount_snapshot',
        'calculated_amount',
    ];

    protected $casts = [
        'reward_value_snapshot' => 'decimal:2',
        'base_amount_snapshot' => 'decimal:2',
        'calculated_amount' => 'decimal:2',
    ];

    public function employeePayroll(): BelongsTo
    {
        return $this->belongsTo(EmployeePayroll::class);
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function conditionSnapshots(): HasMany
    {
        return $this->hasMany(PayrollBonusConditionSnapshot::class);
    }
}
