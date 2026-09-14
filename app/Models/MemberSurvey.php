<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberSurvey extends Model
{
    protected $fillable = [
        'member_id',
        'survey_id',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberData::class, 'member_id');
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(MemberSurveyAnswer::class);
    }

    public function isSubmitted(): bool
    {
        return $this->answers()->exists();
    }
}
