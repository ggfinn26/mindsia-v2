<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationRouting extends Model
{
    protected $fillable = [
        'event_key',
        'position_id',
        'channel',
        'scope',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // scope: global | branch | area
    // channel: in-app | email | telegram

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
