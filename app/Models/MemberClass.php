<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberClass extends Model
{
    protected $table = 'member_class';

    protected $fillable = ['member_registration_id', 'class_id', 'start_date', 'end_date', 'status'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function memberRegistration(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_registration_id');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function curriculumProgress(): HasMany
    {
        return $this->hasMany(MemberCurriculumProgress::class, 'member_class_id');
    }
}
