<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeKpiEvaluation extends Model
{
    protected $fillable = [
        'employee_id',
        'kpi_template_id',
        'employee_name_snapshot',
        'position_name_snapshot',
        'role_name_snapshot',
        'period_type',
        'period_start_date',
        'period_end_date',
        'total_score',
        'grade',
        'status',
        'evaluator_employee_id',
        'evaluator_notes',
        'finalized_at',
    ];

    protected $casts = [
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'total_score' => 'decimal:2',
        'finalized_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(KpiTemplate::class, 'kpi_template_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'evaluator_employee_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(EmployeeKpiEvaluationItem::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(KpiDocument::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }
}
