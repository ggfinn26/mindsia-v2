<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeWorkAttendanceLog extends Model
{
    protected $fillable = [
        'employee_id',
        'branch_id',
        'attendance_date',
        'check_in',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_selfie_telegram_file_id',
        'check_in_distance_m',
        'check_in_notes',
        'check_out',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_selfie_telegram_file_id',
        'check_out_distance_m',
        'check_out_notes',
        'status',
        'late_minutes',
        'early_leave_minutes',
        'is_location_anomaly',
        'anomaly_notes',
        'notes',
        'verified_by_employee_id',
        'verified_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'verified_at' => 'datetime',
        'is_location_anomaly' => 'boolean',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'check_in_distance_m' => 'integer',
        'check_out_distance_m' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'verified_by_employee_id');
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(EmployeeWorkAttendanceAdjustmentHistory::class, 'attendance_log_id');
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('attendance_date', $date);
    }

    public function scopeAnomaly(Builder $query): Builder
    {
        return $query->where('is_location_anomaly', true);
    }

    public function isCheckedIn(): bool
    {
        return $this->check_in !== null && $this->check_out === null;
    }

    public function isPresent(): bool
    {
        return $this->status === 'present';
    }
}
