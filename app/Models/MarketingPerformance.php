<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingPerformance extends Model
{
    protected $fillable = [
        'employee_id',
        'marketing_target_id',
        'period_month',
        'period_year',
        'classes_actual',
        'prospective_members_count',
        'fixed_members_count',
        'registration_value_actual',
        'cash_collected_actual',
        'cash_collected_auto',
        'cash_collected_adjustment',
        'adjustment_notes',
        'status',
    ];

    protected $casts = [
        'registration_value_actual' => 'decimal:2',
        'cash_collected_actual' => 'decimal:2',
        'cash_collected_auto' => 'decimal:2',
        'cash_collected_adjustment' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function marketingTarget(): BelongsTo
    {
        return $this->belongsTo(MarketingTarget::class);
    }
}
