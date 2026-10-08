<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendanceSetting extends Model
{
    protected $fillable = [
        'employee_id',
        'is_required',
        'is_session_required',
        'attendance_scope',
        'early_checkin_minutes',
        'early_checkout_minutes',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_session_required' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
