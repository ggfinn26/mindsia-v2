<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRuleActionExecution extends Model
{
    protected $fillable = [
        'attendance_rule_violation_id',
        'attendance_rule_action_id',
        'status',
        'executed_at',
        'result_notes',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];

    public function violation(): BelongsTo
    {
        return $this->belongsTo(AttendanceRuleViolation::class, 'attendance_rule_violation_id');
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(AttendanceRuleAction::class, 'attendance_rule_action_id');
    }
}
