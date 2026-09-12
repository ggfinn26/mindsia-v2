<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendancePolicySession extends Model
{
    protected $fillable = [
        'attendance_policy_id',
        'is_required',
        'late_tolerance_minutes',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'late_tolerance_minutes' => 'integer',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(AttendancePolicy::class, 'attendance_policy_id');
    }
}
