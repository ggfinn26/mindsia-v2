<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberTestScore extends Model
{
    protected $fillable = [
        'member_test_result_id',
        'test_scoring_criteria_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function memberTestResult(): BelongsTo
    {
        return $this->belongsTo(MemberTestResult::class);
    }

    public function scoringCriteria(): BelongsTo
    {
        return $this->belongsTo(TestScoringCriteria::class, 'test_scoring_criteria_id');
    }
}
