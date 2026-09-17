<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AttendanceRuleAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_rule_id',
        'action_type',
        'action_order',
    ];

    protected $casts = [
        'action_order' => 'integer',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AttendanceRule::class, 'attendance_rule_id');
    }

    public function payrollAction(): HasOne
    {
        return $this->hasOne(AttendanceRulePayrollAction::class);
    }

    public function warningLetterAction(): HasOne
    {
        return $this->hasOne(AttendanceRuleWarningLetterAction::class, 'attendance_rule_action_id');
    }
}
