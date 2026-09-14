<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingKpiRankingRule extends Model
{
    protected $fillable = [
        'rule_code',
        'rule_name',
        'revenue_basis',
        'is_active',
        'created_by_employee_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(MarketingKpiRankingRuleCriterion::class, 'ranking_rule_id')
            ->orderBy('sort_order');
    }
}
