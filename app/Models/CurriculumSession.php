<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumSession extends Model
{
    protected $fillable = [
        'curriculum_id',
        'session_number',
        'session_title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'session_number' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CurriculumItem::class);
    }
}
