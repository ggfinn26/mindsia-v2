<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\FacilityTicket;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class FacilityStatusWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'facility_status';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $baseQuery = FacilityTicket::when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $openTickets = (clone $baseQuery)->whereNotIn('status', ['resolved', 'rejected'])->count();
        $highPriority = (clone $baseQuery)->where('priority', 'high')
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->count();
        $resolvedThisMonth = (clone $baseQuery)->where('status', 'resolved')
            ->whereMonth('updated_at', $now->month)
            ->whereYear('updated_at', $now->year)
            ->count();
        $totalCost = (clone $baseQuery)->whereNotIn('status', ['rejected'])
            ->sum('cost_amount');

        $byStatus = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $byCategory = (clone $baseQuery)
            ->selectRaw('category, COUNT(*) as count')
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        return [
            'open_tickets' => $openTickets,
            'high_priority' => $highPriority,
            'resolved_this_month' => $resolvedThisMonth,
            'total_cost' => $totalCost,
            'by_status' => $byStatus,
            'by_category' => $byCategory,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $tickets = FacilityTicket::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('branch')
            ->orderByDesc('created_at')
            ->get();

        return $tickets->map(fn ($t) => [
            'ticket_number' => $t->ticket_number,
            'title' => $t->title,
            'category' => $t->category,
            'priority' => $t->priority,
            'status' => $t->status,
            'cost' => $t->cost_amount,
            'branch' => $t->branch?->branch_name ?? '-',
            'created_at' => $t->created_at?->format('Y-m-d'),
        ])->toArray();
    }
}
