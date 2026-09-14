<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingKpiManualCriterion extends Model
{
    protected $fillable = [
        'criterion_code',
        'criterion_name',
        'input_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
