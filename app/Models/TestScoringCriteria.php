<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestScoringCriteria extends Model
{
    protected $fillable = [
        'test_id',
        'criteria_name',
        'description',
    ];

    public function classTest(): BelongsTo
    {
        return $this->belongsTo(ClassTest::class, 'test_id');
    }

    public function memberTestScores(): HasMany
    {
        return $this->hasMany(MemberTestScore::class);
    }
}
