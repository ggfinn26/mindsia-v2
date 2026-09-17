<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchRentContract;
use App\Models\BranchRentTermin;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class BranchRentContractsWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'branch_rent_contracts';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $thresholdDays = 90;

        $baseQuery = BranchRentContract::when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $activeContracts = (clone $baseQuery)->where('status', 'active')->count();
        $expiredContracts = (clone $baseQuery)->where('status', 'expired')->count();

        $expiringSoon = (clone $baseQuery)->where('status', 'active')
            ->whereBetween('end_date', [$now->toDateString(), $now->copy()->addDays($thresholdDays)->toDateString()])
            ->count();

        $overdueTermins = BranchRentTermin::where('status', 'overdue')
            ->when($branchId, fn ($q) => $q->whereHas('contract', fn ($c) => $c->where('branch_id', $branchId)))
            ->count();

        $totalRentAmount = (clone $baseQuery)->where('status', 'active')->sum('rent_amount');
        $totalPaidTermins = BranchRentTermin::where('status', 'paid')
            ->when($branchId, fn ($q) => $q->whereHas('contract', fn ($c) => $c->where('branch_id', $branchId)))
            ->sum('amount');
        $totalUnpaidTermins = BranchRentTermin::whereIn('status', ['unpaid', 'overdue'])
            ->when($branchId, fn ($q) => $q->whereHas('contract', fn ($c) => $c->where('branch_id', $branchId)))
            ->sum('amount');

        $byStatus = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'active_contracts' => $activeContracts,
            'expired_contracts' => $expiredContracts,
            'expiring_soon' => $expiringSoon,
            'overdue_termins' => $overdueTermins,
            'total_rent_amount' => $totalRentAmount,
            'total_paid' => $totalPaidTermins,
            'total_unpaid' => $totalUnpaidTermins,
            'by_status' => $byStatus,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $contracts = BranchRentContract::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('branch')
            ->orderByDesc('end_date')
            ->get();

        return $contracts->map(fn ($c) => [
            'branch' => $c->branch?->branch_name ?? '-',
            'owner' => $c->owner_name,
            'rent_amount' => $c->rent_amount,
            'start_date' => $c->start_date?->format('Y-m-d'),
            'end_date' => $c->end_date?->format('Y-m-d'),
            'status' => $c->status,
            'rent_period' => $c->rent_period,
        ])->toArray();
    }
}
