<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumItem extends Model
{
    protected $fillable = [
        'curriculum_session_id',
        'item_name',
        'sequence_number',
        'material_type',
        'material_value',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'sequence_number' => 'integer',
        'is_active' => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CurriculumSession::class, 'curriculum_session_id');
    }
}
