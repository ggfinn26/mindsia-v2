<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingKpiAutomaticCriterion extends Model
{
    protected $fillable = [
        'criterion_code',
        'criterion_name',
        'data_source',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
