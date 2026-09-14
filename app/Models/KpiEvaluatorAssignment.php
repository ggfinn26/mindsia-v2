<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiEvaluatorAssignment extends Model
{
    protected $fillable = [
        'evaluator_employee_id',
        'evaluatee_employee_id',
        'kpi_template_id',
        'effective_start_date',
        'effective_end_date',
        'is_active',
        'created_by_employee_id',
    ];

    protected $casts = [
        'effective_start_date' => 'date',
        'effective_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'evaluator_employee_id');
    }

    public function evaluatee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'evaluatee_employee_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(KpiTemplate::class, 'kpi_template_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEffective($query)
    {
        $today = now()->toDateString();

        return $query->where('effective_start_date', '<=', $today)
            ->where(fn ($q) => $q
                ->whereNull('effective_end_date')
                ->orWhere('effective_end_date', '>=', $today)
            );
    }
}
