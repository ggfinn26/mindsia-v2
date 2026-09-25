<?php

namespace App\Services\Alerts;

use App\Models\FacilityTicket;
use App\Models\InventoryItem;

class FacilityAlertProvider implements AlertProvider
{
    public function alertsFor(?int $branchId): array
    {
        $alerts = [];

        // High-priority tickets unresolved → danger
        $tickets = FacilityTicket::query()
            ->with('branch:id,branch_name')
            ->where('priority', 'high')
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get();

        foreach ($tickets as $ticket) {
            $alerts[] = new StrategicAlert(
                moduleKey: 'facility',
                moduleLabel: 'Fasilitas',
                category: 'Tiket Prioritas Tinggi',
                subject: $ticket->title,
                branchName: $ticket->branch?->branch_name ?? '-',
                detail: 'Status: '.ucfirst($ticket->status),
                url: route('facility.tickets.show', $ticket->id),
                severity: 'danger',
                occurredAt: $ticket->created_at,
            );
        }

        // Damaged / unusable inventory → warning
        $damaged = InventoryItem::query()
            ->with('branch:id,branch_name')
            ->where('status', 'active')
            ->whereIn('condition_status', ['DAMAGED', 'UNUSABLE'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get();

        foreach ($damaged as $item) {
            $alerts[] = new StrategicAlert(
                moduleKey: 'facility',
                moduleLabel: 'Fasilitas',
                category: 'Inventaris Bermasalah',
                subject: $item->item_name,
                branchName: $item->branch?->branch_name ?? '-',
                detail: 'Kondisi: '.ucfirst(strtolower($item->condition_status)),
                url: route('facility.inventory.show', $item->id),
                severity: 'warning',
                occurredAt: $item->updated_at,
            );
        }

        return $alerts;
    }
}
