<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class MemberClass extends Model
{
    protected $table = 'member_class';

    protected $fillable = ['member_registration_id', 'class_id', 'start_date', 'end_date', 'status'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function memberRegistration(): BelongsTo
    {
        return $this->belongsTo(MemberRegistration::class, 'member_registration_id');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function program(): HasOneThrough
    {
        return $this->hasOneThrough(
            Program::class,
            ClassRoom::class,
            'id',
            'id',
            'class_id',
            'program_id'
        );
    }

    public function curriculumProgress(): HasMany
    {
        return $this->hasMany(MemberCurriculumProgress::class, 'member_class_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(MemberAttendance::class, 'member_class_id');
    }

    public function sessionAssessments(): HasMany
    {
        return $this->hasMany(MemberSessionAssessment::class, 'member_class_id');
    }
}
