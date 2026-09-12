<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingBonusRuleTier extends Model
{
    protected $fillable = [
        'marketing_bonus_rule_id',
        'minimum_tenure_months',
        'maximum_tenure_months',
        'minimum_achievement_percentage',
        'maximum_achievement_percentage',
        'reward_type',
        'reward_value',
    ];

    protected $casts = [
        'minimum_tenure_months' => 'integer',
        'maximum_tenure_months' => 'integer',
        'minimum_achievement_percentage' => 'decimal:2',
        'maximum_achievement_percentage' => 'decimal:2',
        'reward_value' => 'decimal:2',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(MarketingBonusRule::class, 'marketing_bonus_rule_id');
    }
}
