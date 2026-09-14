<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeKpiEvaluationItem extends Model
{
    protected $fillable = [
        'employee_kpi_evaluation_id',
        'kpi_template_indicator_id',
        'indicator_code_snapshot',
        'indicator_name_snapshot',
        'unit_snapshot',
        'weight_snapshot',
        'target_value',
        'actual_value',
        'achievement_percentage',
        'score',
        'source_type',
        'source_id',
        'notes',
    ];

    protected $casts = [
        'weight_snapshot' => 'decimal:2',
        'target_value' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'achievement_percentage' => 'decimal:2',
        'score' => 'decimal:2',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(EmployeeKpiEvaluation::class, 'employee_kpi_evaluation_id');
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(KpiTemplateIndicator::class, 'kpi_template_indicator_id');
    }
}
