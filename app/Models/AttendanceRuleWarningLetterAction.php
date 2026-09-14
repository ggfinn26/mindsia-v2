<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRuleWarningLetterAction extends Model
{
    protected $fillable = [
        'attendance_rule_action_id',
        'letter_template_id',
        'is_cumulative',
    ];

    protected $casts = [
        'is_cumulative' => 'boolean',
    ];

    public function action()
    {
        return $this->belongsTo(AttendanceRuleAction::class, 'attendance_rule_action_id');
    }

    public function template()
    {
        return $this->belongsTo(LetterTemplate::class, 'letter_template_id');
    }
}
