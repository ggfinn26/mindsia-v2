<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToeflQuestion extends Model
{
    protected $table = 'toefl_questions';

    protected $hidden = ['correct_option', 'explanation'];

    protected $fillable = [
        'toefl_test_id',
        'passage_id',
        'section',
        'display_order',
        'question_text',
        'image_media_id',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'difficulty',
        'points',
        'explanation',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(ToeflTest::class, 'toefl_test_id');
    }

    public function passage(): BelongsTo
    {
        return $this->belongsTo(ToeflPassage::class, 'passage_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(ToeflMedia::class, 'image_media_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ToeflAnswer::class, 'toefl_question_id');
    }
}
