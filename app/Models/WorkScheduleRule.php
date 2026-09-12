<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkScheduleRule extends Model
{
    protected $fillable = [
        'setting_name',
        'start_time',
        'end_time',
        'break_start_time',
        'break_end_time',
        'late_tolerance_minutes',
        'early_leave_tolerance_minutes',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'late_tolerance_minutes' => 'integer',
        'early_leave_tolerance_minutes' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(WorkScheduleAssignment::class);
    }
}
