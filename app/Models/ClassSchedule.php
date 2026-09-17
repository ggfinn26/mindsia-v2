<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['class_id', 'schedule_date', 'start_time', 'end_time', 'late_tolerance_minutes', 'material_taught'];

    protected $casts = ['schedule_date' => 'date'];

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function memberAttendances(): HasMany
    {
        return $this->hasMany(MemberAttendance::class, 'class_schedule_id');
    }
}
