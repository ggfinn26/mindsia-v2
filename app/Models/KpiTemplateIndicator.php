<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiTemplateIndicator extends Model
{
    protected $fillable = [
        'kpi_template_id',
        'indicator_code',
        'indicator_name',
        'description',
        'unit',
        'target_value',
        'weight',
        'sequence_number',
        'data_source_type',
        'is_active',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(KpiTemplate::class, 'kpi_template_id');
    }

    public function isAutomatic(): bool
    {
        return $this->data_source_type !== null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
