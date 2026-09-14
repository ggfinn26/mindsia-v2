<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetEstimateAttachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'budget_estimate_id', 'file_id', 'file_name', 'file_type', 'uploaded_by',
    ];

    public function budgetEstimate(): BelongsTo
    {
        return $this->belongsTo(BudgetEstimate::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by');
    }
}
