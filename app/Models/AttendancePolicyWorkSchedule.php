<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendancePolicyWorkSchedule extends Model
{
    protected $fillable = [
        'attendance_policy_id',
        'is_required',
        'late_tolerance_minutes',
        'early_leave_tolerance_minutes',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'late_tolerance_minutes' => 'integer',
        'early_leave_tolerance_minutes' => 'integer',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(AttendancePolicy::class, 'attendance_policy_id');
    }
}
