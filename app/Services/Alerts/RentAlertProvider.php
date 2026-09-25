<?php

namespace App\Services\Alerts;

use App\Models\BranchRentContract;
use App\Models\BranchRentTermin;
use Carbon\Carbon;

class RentAlertProvider implements AlertProvider
{
    public function alertsFor(?int $branchId): array
    {
        $alerts = [];

        // Overdue termins → danger
        $overdueTermins = BranchRentTermin::query()
            ->with('contract.branch:id,branch_name')
            ->where('status', 'overdue')
            ->when($branchId, fn ($q) => $q->whereHas('contract', fn ($c) => $c->where('branch_id', $branchId)))
            ->get();

        foreach ($overdueTermins as $termin) {
            $branch = $termin->contract?->branch;
            $alerts[] = new StrategicAlert(
                moduleKey: 'rent',
                moduleLabel: 'Sewa Cabang',
                category: 'Termin Overdue',
                subject: $branch?->branch_name ?? '-',
                branchName: $branch?->branch_name ?? '-',
                detail: 'Termin ke-'.$termin->termin_number.' belum dibayar',
                url: route('facility.rent-contracts.show', $termin->contract_id),
                severity: 'danger',
                occurredAt: $termin->due_date ? Carbon::parse($termin->due_date) : null,
            );
        }

        // Contracts expiring ≤ 30 days → warning
        $expiring = BranchRentContract::query()
            ->with('branch:id,branch_name')
            ->where('status', 'active')
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get();

        foreach ($expiring as $contract) {
            $daysLeft = (int) now()->startOfDay()->diffInDays(Carbon::parse($contract->end_date)->startOfDay(), false);
            $alerts[] = new StrategicAlert(
                moduleKey: 'rent',
                moduleLabel: 'Sewa Cabang',
                category: 'Kontrak Segera Berakhir',
                subject: $contract->branch?->branch_name ?? '-',
                branchName: $contract->branch?->branch_name ?? '-',
                detail: "Sisa {$daysLeft} hari",
                url: route('facility.rent-contracts.show', $contract->id),
                severity: 'warning',
                occurredAt: Carbon::parse($contract->end_date),
            );
        }

        return $alerts;
    }
}
