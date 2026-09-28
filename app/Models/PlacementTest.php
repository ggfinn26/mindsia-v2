<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementTest extends Model
{
    protected $fillable = [
        'class_id',
        'member_data_id',
        'attended_writing',
        'attended_speaking',
        'score_writing',
        'score_speaking',
        'final_score',
        'level',
        'is_scheduled',
        'recorded_by',
    ];

    protected $casts = [
        'attended_writing' => 'boolean',
        'attended_speaking' => 'boolean',
        'is_scheduled' => 'boolean',
        'final_score' => 'float',
    ];

    /**
     * Thresholds: ≤51 basic, ≤81 intermediate, >81 advanced.
     * Source: PRE-TEST ASSESSMENT RUBRIC.xlsx + V1 PlacementTest::computeLevel()
     */
    public static function computeLevel(float $finalScore): string
    {
        if ($finalScore <= 51) {
            return 'basic';
        }

        if ($finalScore <= 81) {
            return 'intermediate';
        }

        return 'advanced';
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function memberData(): BelongsTo
    {
        return $this->belongsTo(MemberData::class, 'member_data_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
