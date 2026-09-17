<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberAttendance extends Model
{
    protected $table = 'member_attendance';

    protected $fillable = [
        'member_class_id',
        'class_schedule_id',
        'status',
        'notes',
        'recorded_by_employee_id',
    ];

    public function memberClass(): BelongsTo
    {
        return $this->belongsTo(MemberClass::class);
    }

    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by_employee_id');
    }
}
