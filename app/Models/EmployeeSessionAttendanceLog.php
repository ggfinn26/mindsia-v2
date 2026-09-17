<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeSessionAttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_schedule_id',
        'employee_id',
        'status',
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
        'late_minutes',
        'is_location_anomaly',
        'anomaly_notes',
        'verified_by_employee_id',
        'verified_at',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'verified_at' => 'datetime',
        'is_location_anomaly' => 'boolean',
        'late_minutes' => 'integer',
        'check_in_distance_m' => 'integer',
        'check_out_distance_m' => 'integer',
    ];

    public function sessionSchedule(): BelongsTo
    {
        return $this->belongsTo(SessionSchedule::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'verified_by_employee_id');
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(EmployeeSessionAttendanceAdjustmentHistory::class, 'attendance_log_id');
    }

    public function scopeAnomaly(Builder $query): Builder
    {
        return $query->where('is_location_anomaly', true);
    }

    public function isCheckedIn(): bool
    {
        return $this->check_in !== null && $this->check_out === null;
    }
}
