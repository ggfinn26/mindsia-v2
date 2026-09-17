<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiBonusRuleTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'kpi_bonus_rule_id',
        'minimum_score',
        'maximum_score',
        'reward_type',
        'reward_value',
    ];

    protected $casts = [
        'minimum_score' => 'decimal:2',
        'maximum_score' => 'decimal:2',
        'reward_value' => 'decimal:2',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(KpiBonusRule::class, 'kpi_bonus_rule_id');
    }
}
