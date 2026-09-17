<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialBonusRuleCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'special_bonus_rule_id',
        'metric_code',
        'period_type',
        'operator',
        'target_value',
        'data_source',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(SpecialBonusRule::class, 'special_bonus_rule_id');
    }
}
