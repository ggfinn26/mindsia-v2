<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingKpiRankingRuleCriterion extends Model
{
    protected $fillable = [
        'ranking_rule_id',
        'automatic_criterion_id',
        'manual_criterion_id',
        'sort_order',
        'sort_direction',
    ];

    public function rankingRule(): BelongsTo
    {
        return $this->belongsTo(MarketingKpiRankingRule::class, 'ranking_rule_id');
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
