<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingKpiSnapshot extends Model
{
    protected $fillable = [
        'employee_id',
        'marketing_target_id',
        'marketing_performance_id',
        'ranking_rule_id',
        'ranking_rule_code_snapshot',
        'ranking_rule_name_snapshot',
        'revenue_basis_snapshot',
        'employee_name_snapshot',
        'branch_name_snapshot',
        'area_name_snapshot',
        'region_name_snapshot',
        'period_month',
        'period_year',
        'classes_target',
        'classes_actual',
        'classes_percentage',
        'prospective_members_count',
        'fixed_members_count',
        'omzet_target',
        'registration_value_actual',
        'registration_value_percentage',
        'cash_collected_actual',
        'cash_collected_percentage',
        'mpi_score',
        'rank_area',
        'rank_region',
        'rank_national',
        'finalized_at',
    ];

    protected $casts = [
        'classes_percentage' => 'decimal:2',
        'omzet_target' => 'decimal:2',
        'registration_value_actual' => 'decimal:2',
        'registration_value_percentage' => 'decimal:2',
        'cash_collected_actual' => 'decimal:2',
        'cash_collected_percentage' => 'decimal:2',
        'mpi_score' => 'decimal:2',
        'finalized_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function marketingTarget(): BelongsTo
    {
        return $this->belongsTo(MarketingTarget::class);
    }

    public function marketingPerformance(): BelongsTo
    {
        return $this->belongsTo(MarketingPerformance::class);
    }

    public function rankingRule(): BelongsTo
    {
        return $this->belongsTo(MarketingKpiRankingRule::class, 'ranking_rule_id');
    }

    public function snapshotCriteria(): HasMany
    {
        return $this->hasMany(MarketingKpiSnapshotCriterion::class, 'marketing_kpi_snapshot_id')
            ->orderBy('sort_order_snapshot');
    }

    public function isFinalized(): bool
    {
        return $this->finalized_at !== null;
    }
}
