<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'branch_id',
        'budget_estimate_item_id',
        'item_code',
        'item_name',
        'category',
        'inventory_type',
        'quantity',
        'unit',
        'condition_status',
        'purchase_date',
        'purchase_price',
        'input_date',
        'location',
        'status',
        'disposal_reason',
        'disposed_at',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'input_date' => 'date',
        'purchase_price' => 'decimal:2',
        'disposed_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function budgetEstimateItem(): BelongsTo
    {
        return $this->belongsTo(BudgetEstimateItem::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(InventoryItemHistory::class)->orderBy('changed_at');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
