<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumInteractiveQuestion extends Model
{
    protected $fillable = [
        'block_id',
        'sort_order',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'explanation',
    ];

    protected $hidden = ['correct_option', 'explanation'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(CurriculumInteractiveBlock::class, 'block_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CurriculumInteractiveAnswer::class, 'question_id');
    }
}
