<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = [
        'type',
        'label',
        'key_value',
        'is_active',
        'last_used_at',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    protected $hidden = ['key_value'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
