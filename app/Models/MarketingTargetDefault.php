<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingTargetDefault extends Model
{
    protected $fillable = [
        'position_id',
        'period_month',
        'period_year',
        'classes_target',
        'omzet_target',
        'created_by_employee_id',
    ];

    protected $casts = [
        'omzet_target' => 'decimal:2',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }
}
