<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_name',
        'attendance_type',
        'trigger_type',
        'trigger_operator',
        'trigger_value',
        'period_type',
        'is_active',
    ];

    protected $casts = [
        'trigger_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function actions(): HasMany
    {
        return $this->hasMany(AttendanceRuleAction::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(AttendanceRuleViolation::class);
    }
}
