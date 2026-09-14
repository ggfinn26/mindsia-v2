<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetEstimateItem extends Model
{
    protected $fillable = [
        'budget_estimate_id', 'item_name', 'quantity', 'estimated_price', 'total_price', 'notes',
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function budgetEstimate(): BelongsTo
    {
        return $this->belongsTo(BudgetEstimate::class);
    }
}
