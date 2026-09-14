<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Webhook extends Model
{
    protected $fillable = [
        'bot_id',
        'name',
        'endpoint_url',
        'last_ping_at',
        'last_status',
        'notes',
    ];

    protected $casts = [
        'last_ping_at' => 'datetime',
    ];

    // last_status: ok | failed | unknown

    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }

    public function isHealthy(): bool
    {
        return $this->last_status === 'ok';
    }
}
