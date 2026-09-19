<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeWarningLetter extends Model
{
    protected $fillable = [
        'employee_id',
        'letter_template_id',
        'attendance_rule_violation_id',
        'sp_level',
        'letter_number',
        'telegram_file_id',
        'storage_path',
        'issued_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'sp_level' => 'integer',
        'issued_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function template()
    {
        return $this->belongsTo(LetterTemplate::class, 'letter_template_id');
    }

    public function violation()
    {
        return $this->belongsTo(AttendanceRuleViolation::class, 'attendance_rule_violation_id');
    }
}
