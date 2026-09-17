<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\ProspectiveMember;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class LeadStatusWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'lead_status';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $byStatus = ProspectiveMember::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $total = array_sum($byStatus);
        $registered = $byStatus['registered'] ?? 0;
        $closingRate = $total > 0 ? round(($registered / $total) * 100, 1) : 0;

        $newThisMonth = ProspectiveMember::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $followUpPending = ProspectiveMember::where('status', 'follow_up')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        return [
            'by_status' => $byStatus,
            'total' => $total,
            'new_this_month' => $newThisMonth,
            'follow_up_pending' => $followUpPending,
            'closing_rate' => $closingRate,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $prospects = ProspectiveMember::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('branch:id,branch_name')
            ->orderBy('created_at', 'desc')
            ->get();

        return $prospects->map(fn ($p) => [
            'name' => $p->full_name ?? '-',
            'phone' => $p->phone ?? '-',
            'status' => $p->status,
            'branch' => $p->branch?->branch_name ?? '-',
            'created_at' => $p->created_at?->format('Y-m-d'),
        ])->toArray();
    }
}
