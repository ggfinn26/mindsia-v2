<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkScheduleRule extends Model
{
    use HasFactory;

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

    public function workDays(): HasMany
    {
        return $this->hasMany(WorkScheduleDay::class);
    }

    /** Empty work_schedule_days = applies every day. */
    public function isActiveOnDate(string $date): bool
    {
        if ($this->workDays->isEmpty()) {
            return true;
        }

        return $this->workDays->pluck('day_of_week')->contains(
            (int) now()->parse($date)->dayOfWeekIso
        );
    }
}
