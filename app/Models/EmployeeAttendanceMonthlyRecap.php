<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendanceMonthlyRecap extends Model
{
    protected $fillable = [
        'employee_id',
        'period_year',
        'period_month',
        'total_scheduled_working_days',
        'total_effective_working_days',
        'total_present',
        'total_checked_in',
        'total_absent',
        'total_late',
        'total_late_minutes',
        'total_early_leave',
        'total_sick',
        'total_permission',
        'total_leave',
        'total_holiday',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeForPeriod(Builder $query, int $year, int $month): Builder
    {
        return $query->where('period_year', $year)->where('period_month', $month);
    }
}
