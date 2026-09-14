<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardWidgetConfig extends Model
{
    protected $fillable = [
        'position_id',
        'widget_key',
        'order',
        'is_enabled',
        'custom_settings',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'custom_settings' => 'array',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
