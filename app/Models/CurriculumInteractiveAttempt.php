<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumInteractiveAttempt extends Model
{
    protected $fillable = [
        'member_class_id',
        'curriculum_item_id',
        'started_at',
        'completed_at',
        'score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'integer',
    ];

    public function memberClass(): BelongsTo
    {
        return $this->belongsTo(MemberClass::class, 'member_class_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CurriculumItem::class, 'curriculum_item_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CurriculumInteractiveAnswer::class, 'attempt_id');
    }
}
