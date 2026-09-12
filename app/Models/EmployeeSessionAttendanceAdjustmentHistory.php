<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSessionAttendanceAdjustmentHistory extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'attendance_log_id',
        'adjusted_by_employee_id',
        'previous_status',
        'new_status',
        'previous_check_in',
        'new_check_in',
        'previous_check_out',
        'new_check_out',
        'adjustment_reason',
    ];

    protected $casts = [
        'previous_check_in' => 'datetime',
        'new_check_in' => 'datetime',
        'previous_check_out' => 'datetime',
        'new_check_out' => 'datetime',
    ];

    public function attendanceLog(): BelongsTo
    {
        return $this->belongsTo(EmployeeSessionAttendanceLog::class, 'attendance_log_id');
    }

    public function adjustedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'adjusted_by_employee_id');
    }
}
