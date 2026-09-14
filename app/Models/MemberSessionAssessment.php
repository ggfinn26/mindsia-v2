<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberSessionAssessment extends Model
{
    protected $fillable = [
        'member_class_id',
        'class_schedule_id',
        'employee_id',
        'score',
        'notes',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function memberClass(): BelongsTo
    {
        return $this->belongsTo(MemberClass::class);
    }

    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
