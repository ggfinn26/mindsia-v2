<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToeflAnswer extends Model
{
    protected $table = 'toefl_answers';

    protected $fillable = [
        'toefl_session_id',
        'toefl_question_id',
        'selected_option',
        'is_correct',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ToeflSession::class, 'toefl_session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ToeflQuestion::class, 'toefl_question_id');
    }
}
