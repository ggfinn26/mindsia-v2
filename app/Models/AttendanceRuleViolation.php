<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceRuleViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attendance_rule_id',
        'period_start_date',
        'period_end_date',
        'trigger_value',
    ];

    protected $casts = [
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'trigger_value' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AttendanceRule::class, 'attendance_rule_id');
    }

    public function actionExecutions(): HasMany
    {
        return $this->hasMany(AttendanceRuleActionExecution::class, 'attendance_rule_violation_id');
    }
}
