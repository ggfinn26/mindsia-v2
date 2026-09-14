<?php

namespace App\Repositories\Facility;

use App\Models\InventoryItem;
use App\Models\InventoryItemHistory;
use Illuminate\Database\Eloquent\Collection;

class InventoryItemRepository
{
    public function find(int $id): InventoryItem
    {
        return InventoryItem::with(['branch', 'budgetEstimateItem', 'histories'])->findOrFail($id);
    }

    public function byBranch(int $branchId, ?string $status = 'active'): Collection
    {
        return InventoryItem::where('branch_id', $branchId)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('item_name')
            ->get();
    }

    public function create(array $data, int $employeeId): InventoryItem
    {
        $item = InventoryItem::create($data);

        InventoryItemHistory::create([
            'inventory_item_id' => $item->id,
            'change_type' => 'created',
            'quantity_after' => $item->quantity,
            'condition_after' => $item->condition_status,
            'location_after' => $item->location,
            'changed_by_employee_id' => $employeeId,
            'changed_at' => now(),
        ]);

        return $item;
    }

    public function update(InventoryItem $item, array $data, int $employeeId): InventoryItem
    {
        $item->update($data);

        InventoryItemHistory::create([
            'inventory_item_id' => $item->id,
            'change_type' => 'updated',
            'changed_by_employee_id' => $employeeId,
            'changed_at' => now(),
        ]);

        return $item;
    }

    public function adjustQuantity(InventoryItem $item, int $newQuantity, string $reason, int $employeeId): InventoryItem
    {
        $before = $item->quantity;
        $item->update(['quantity' => $newQuantity]);

        InventoryItemHistory::create([
            'inventory_item_id' => $item->id,
            'change_type' => 'quantity_adjusted',
            'quantity_before' => $before,
            'quantity_after' => $newQuantity,
            'reason' => $reason,
            'changed_by_employee_id' => $employeeId,
            'changed_at' => now(),
        ]);

        return $item;
    }

    public function changeCondition(InventoryItem $item, string $newCondition, string $reason, int $employeeId): InventoryItem
    {
        $before = $item->condition_status;
        $item->update(['condition_status' => $newCondition]);

        InventoryItemHistory::create([
            'inventory_item_id' => $item->id,
            'change_type' => 'condition_changed',
            'condition_before' => $before,
            'condition_after' => $newCondition,
            'reason' => $reason,
            'changed_by_employee_id' => $employeeId,
            'changed_at' => now(),
        ]);

        return $item;
    }

    public function changeLocation(InventoryItem $item, string $newLocation, string $reason, int $employeeId): InventoryItem
    {
        $before = $item->location;
        $item->update(['location' => $newLocation]);

        InventoryItemHistory::create([
            'inventory_item_id' => $item->id,
            'change_type' => 'location_changed',
            'location_before' => $before,
            'location_after' => $newLocation,
            'reason' => $reason,
            'changed_by_employee_id' => $employeeId,
            'changed_at' => now(),
        ]);

        return $item;
    }

    public function dispose(InventoryItem $item, string $reason, int $employeeId): InventoryItem
    {
        $item->update([
            'status' => 'disposed',
            'disposal_reason' => $reason,
            'disposed_at' => now(),
        ]);

        InventoryItemHistory::create([
            'inventory_item_id' => $item->id,
            'change_type' => 'disposed',
            'reason' => $reason,
            'changed_by_employee_id' => $employeeId,
            'changed_at' => now(),
        ]);

        return $item;
    }
}
