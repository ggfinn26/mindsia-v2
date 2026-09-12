<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollComponent extends Model
{
    protected $fillable = [
        'component_code',
        'component_name',
        'component_type',
        'calculation_method',
        'is_taxable',
        'is_active',
    ];

    protected $casts = [
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function employeeCompensations(): HasMany
    {
        return $this->hasMany(EmployeeCompensation::class);
    }

    public function attendanceRulePayrollAction(): HasOne
    {
        return $this->hasOne(AttendanceRulePayrollAction::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
