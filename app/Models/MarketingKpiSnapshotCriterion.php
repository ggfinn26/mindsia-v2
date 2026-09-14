<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingKpiSnapshotCriterion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'marketing_kpi_snapshot_id',
        'automatic_criterion_id',
        'manual_criterion_id',
        'criterion_code_snapshot',
        'criterion_name_snapshot',
        'criterion_value',
        'value_source',
        'sort_order_snapshot',
        'sort_direction_snapshot',
    ];

    protected $casts = [
        'criterion_value' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(MarketingKpiSnapshot::class, 'marketing_kpi_snapshot_id');
    }

    public function automaticCriterion(): BelongsTo
    {
        return $this->belongsTo(MarketingKpiAutomaticCriterion::class, 'automatic_criterion_id');
    }

    public function manualCriterion(): BelongsTo
    {
        return $this->belongsTo(MarketingKpiManualCriterion::class, 'manual_criterion_id');
    }
}
