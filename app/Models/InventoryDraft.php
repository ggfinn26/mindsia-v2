<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryDraft extends Model
{
    protected $fillable = ['budget_estimate_id', 'status', 'created_by_employee_id'];

    public function budgetEstimate(): BelongsTo
    {
        return $this->belongsTo(BudgetEstimate::class);
    }
}
