<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\InventoryItem;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class InventoryStatusWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'inventory_status';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $baseQuery = InventoryItem::when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $activeItems = (clone $baseQuery)->where('status', 'active')->count();
        $disposedItems = (clone $baseQuery)->where('status', 'disposed')->count();
        $needsRepair = (clone $baseQuery)->where('status', 'active')
            ->whereIn('condition_status', ['NEEDS_REPAIR', 'DAMAGED'])
            ->count();
        $unusableItems = (clone $baseQuery)->where('status', 'active')
            ->where('condition_status', 'UNUSABLE')
            ->count();
        $totalValue = (clone $baseQuery)->where('status', 'active')
            ->selectRaw('SUM(purchase_price * quantity) as value')
            ->value('value') ?? 0;

        $byType = (clone $baseQuery)->where('status', 'active')
            ->selectRaw('inventory_type, COUNT(*) as count')
            ->groupBy('inventory_type')
            ->pluck('count', 'inventory_type')
            ->toArray();

        $byCondition = (clone $baseQuery)->where('status', 'active')
            ->selectRaw('condition_status, COUNT(*) as count')
            ->groupBy('condition_status')
            ->pluck('count', 'condition_status')
            ->toArray();

        return [
            'active_items' => $activeItems,
            'disposed_items' => $disposedItems,
            'needs_repair' => $needsRepair,
            'unusable' => $unusableItems,
            'total_value' => $totalValue,
            'by_type' => $byType,
            'by_condition' => $byCondition,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $items = InventoryItem::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('branch')
            ->where('status', 'active')
            ->orderBy('item_name')
            ->get();

        return $items->map(fn ($i) => [
            'item_code' => $i->item_code,
            'item_name' => $i->item_name,
            'category' => $i->category,
            'type' => $i->inventory_type,
            'quantity' => $i->quantity,
            'unit' => $i->unit,
            'condition' => $i->condition_status,
            'location' => $i->location,
            'purchase_price' => $i->purchase_price,
            'branch' => $i->branch?->branch_name ?? '-',
        ])->toArray();
    }
}
