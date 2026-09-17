<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_month',
        'period_year',
        'status',
        'pay_date',
        'confirmed_by_employee_id',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'confirmed_at' => 'datetime',
        'period_month' => 'integer',
        'period_year' => 'integer',
    ];

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'confirmed_by_employee_id');
    }

    public function employeePayrolls(): HasMany
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isReview(): bool
    {
        return $this->status === 'review';
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }
}
