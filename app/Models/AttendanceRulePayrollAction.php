<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRulePayrollAction extends Model
{
    protected $fillable = [
        'attendance_rule_action_id',
        'payroll_component_id',
        'deduction_amount',
    ];

    protected $casts = [
        'deduction_amount' => 'decimal:2',
    ];

    public function ruleAction(): BelongsTo
    {
        return $this->belongsTo(AttendanceRuleAction::class, 'attendance_rule_action_id');
    }
}
