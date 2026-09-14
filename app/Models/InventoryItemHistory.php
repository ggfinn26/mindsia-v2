<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItemHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'inventory_item_id',
        'change_type',
        'quantity_before',
        'quantity_after',
        'condition_before',
        'condition_after',
        'location_before',
        'location_after',
        'reason',
        'changed_by_employee_id',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'changed_by_employee_id');
    }
}
