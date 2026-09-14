<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bot extends Model
{
    protected $fillable = [
        'name',
        'type',
        'token',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = ['token'];

    // type: telegram | whatsapp

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }
}
