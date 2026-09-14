<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassTest extends Model
{
    protected $table = 'test';

    protected $fillable = [
        'test_name',
        'test_type',
        'program_id',
        'class_id',
        'date',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function scoringCriteria(): HasMany
    {
        return $this->hasMany(TestScoringCriteria::class, 'test_id');
    }

    public function memberTestResults(): HasMany
    {
        return $this->hasMany(MemberTestResult::class, 'test_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class, 'test_id');
    }
}
