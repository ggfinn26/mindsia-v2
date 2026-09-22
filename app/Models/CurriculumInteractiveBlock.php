<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumInteractiveBlock extends Model
{
    protected $fillable = [
        'curriculum_item_id',
        'sort_order',
        'instruction_text',
        'audio_media_id',
        'image_media_id',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(CurriculumItem::class, 'curriculum_item_id');
    }

    public function audio(): BelongsTo
    {
        return $this->belongsTo(CurriculumMedia::class, 'audio_media_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(CurriculumMedia::class, 'image_media_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(CurriculumInteractiveQuestion::class, 'block_id')->orderBy('sort_order');
    }
}
