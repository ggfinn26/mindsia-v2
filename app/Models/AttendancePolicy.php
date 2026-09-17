<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AttendancePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_name',
        'attendance_scope',
        'branch_id',
        'area_id',
        'region_id',
        'is_attendance_exempt',
        'exemption_reason',
        'is_active',
    ];

    protected $casts = [
        'is_attendance_exempt' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function workScheduleConfig(): HasOne
    {
        return $this->hasOne(AttendancePolicyWorkSchedule::class);
    }

    public function sessionConfig(): HasOne
    {
        return $this->hasOne(AttendancePolicySession::class);
    }

    public function policyRules(): HasMany
    {
        return $this->hasMany(AttendancePolicyRule::class);
    }
}
