<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayrollPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_payroll_id',
        'payment_status',
        'payment_method',
        'amount',
        'paid_at',
        'payment_reference',
        'telegram_proof_id',
        'proof_path',
        'failure_reason',
        'paid_by_employee_id',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function employeePayroll(): BelongsTo
    {
        return $this->belongsTo(EmployeePayroll::class);
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'paid_by_employee_id');
    }
}
