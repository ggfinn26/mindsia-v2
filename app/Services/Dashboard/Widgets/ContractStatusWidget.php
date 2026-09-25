<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\ContractExtendOffer;
use App\Models\EmploymentStatus;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class ContractStatusWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'contract_status';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $thresholdDays = 30;

        $baseQuery = EmploymentStatus::when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->whereDoesntHave('offBoarding');

        $activeContracts = (clone $baseQuery)->where('type_employment', 'contract')->where('contract_end_date', '>=', $now->toDateString())->count();
        $extendedContracts = 0; // Or whatever logic maps to extended
        $suspendedContracts = 0; // Not available in schema

        $expiringSoon = (clone $baseQuery)->where('type_employment', 'contract')
            ->whereBetween('contract_end_date', [$now->toDateString(), $now->copy()->addDays($thresholdDays)->toDateString()])
            ->count();

        $pendingExtensions = ContractExtendOffer::where('status', 'pending')
            ->when($branchId, fn ($q) => $q->whereHas('employmentStatus.employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        $byStatus = EmploymentStatus::when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->selectRaw('type_employment as contract_status, COUNT(*) as count')
            ->whereDoesntHave('offBoarding')
            ->groupBy('type_employment')
            ->pluck('count', 'contract_status')
            ->toArray();

        return [
            'active_contracts' => $activeContracts,
            'extended_contracts' => $extendedContracts,
            'suspended_contracts' => $suspendedContracts,
            'expiring_soon' => $expiringSoon,
            'pending_extensions' => $pendingExtensions,
            'by_status' => $byStatus,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $statuses = EmploymentStatus::when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->with(['employee', 'position'])
            ->whereDoesntHave('offBoarding')
            ->orderBy('contract_end_date')
            ->get();

        return $statuses->map(fn ($s) => [
            'employee' => $s->employee?->full_name ?? '-',
            'position' => $s->position?->position_name ?? '-',
            'contract_status' => $s->type_employment,
            'join_date' => $s->join_date?->format('Y-m-d'),
            'contract_start' => $s->contract_start_date?->format('Y-m-d'),
            'contract_end' => $s->contract_end_date?->format('Y-m-d'),
        ])->toArray();
    }
}
