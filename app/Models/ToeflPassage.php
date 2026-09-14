<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToeflPassage extends Model
{
    protected $table = 'toefl_passages';

    protected $fillable = [
        'toefl_test_id',
        'section',
        'title',
        'body_text',
        'audio_media_id',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(ToeflTest::class, 'toefl_test_id');
    }

    public function audio(): BelongsTo
    {
        return $this->belongsTo(ToeflMedia::class, 'audio_media_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ToeflQuestion::class, 'passage_id');
    }
}
