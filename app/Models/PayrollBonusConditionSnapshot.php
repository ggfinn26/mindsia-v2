<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollBonusConditionSnapshot extends Model
{
    protected $fillable = [
        'payroll_bonus_calculation_id',
        'metric_code_snapshot',
        'data_source_snapshot',
        'period_type_snapshot',
        'operator_snapshot',
        'target_value_snapshot',
        'actual_value_snapshot',
        'condition_passed',
    ];

    protected $casts = [
        'target_value_snapshot' => 'decimal:2',
        'actual_value_snapshot' => 'decimal:2',
        'condition_passed' => 'boolean',
    ];

    public function bonusCalculation(): BelongsTo
    {
        return $this->belongsTo(PayrollBonusCalculation::class);
    }
}
