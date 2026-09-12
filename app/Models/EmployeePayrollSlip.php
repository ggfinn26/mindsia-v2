<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayrollSlip extends Model
{
    protected $fillable = [
        'employee_payroll_id',
        'telegram_file_id',
        'generated_at',
        'generated_by_employee_id',
        'signatory_employee_id',
        'signatory_name_snapshot',
        'signatory_position_snapshot',
        'signature_data_snapshot',
        'signed_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function employeePayroll(): BelongsTo
    {
        return $this->belongsTo(EmployeePayroll::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'generated_by_employee_id');
    }

    public function signatory(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'signatory_employee_id');
    }
}
