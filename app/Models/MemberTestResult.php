<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberTestResult extends Model
{
    protected $fillable = [
        'member_class_id',
        'test_id',
        'level',
        'final_score',
    ];

    protected $casts = [
        'final_score' => 'integer',
    ];

    public function memberClass(): BelongsTo
    {
        return $this->belongsTo(MemberClass::class);
    }

    public function classTest(): BelongsTo
    {
        return $this->belongsTo(ClassTest::class, 'test_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(MemberTestScore::class);
    }
}
