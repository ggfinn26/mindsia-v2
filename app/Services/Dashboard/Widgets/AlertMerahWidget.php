<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\AttendanceRuleViolation;
use App\Models\BranchRentContract;
use App\Models\BranchRentTermin;
use App\Models\EmployeeWarningLetter;
use App\Models\EmploymentStatus;
use App\Models\FacilityTicket;
use App\Models\InventoryItem;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class AlertMerahWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'alert_merah';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $alerts = [];

        // 1. Employee contracts expiring within 30 days
        $expiringContracts = EmploymentStatus::whereIn('contract_status', ['active', 'extended'])
            ->whereBetween('contract_end_date', [$now->toDateString(), $now->copy()->addDays(30)->toDateString()])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereDoesntHave('offBoarding')
            ->count();

        if ($expiringContracts > 0) {
            $alerts[] = ['type' => 'contract_expiring', 'label' => 'Kontrak Karyawan Segera Berakhir', 'count' => $expiringContracts, 'severity' => 'warning'];
        }

        // 2. Rent contracts expiring within 90 days
        $expiringRent = BranchRentContract::where('status', 'active')
            ->whereBetween('end_date', [$now->toDateString(), $now->copy()->addDays(90)->toDateString()])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        if ($expiringRent > 0) {
            $alerts[] = ['type' => 'rent_expiring', 'label' => 'Kontrak Sewa Segera Berakhir', 'count' => $expiringRent, 'severity' => 'warning'];
        }

        // 3. Overdue rent termins
        $overdueTermins = BranchRentTermin::where('status', 'overdue')
            ->when($branchId, fn ($q) => $q->whereHas('contract', fn ($c) => $c->where('branch_id', $branchId)))
            ->count();

        if ($overdueTermins > 0) {
            $alerts[] = ['type' => 'rent_overdue', 'label' => 'Termin Sewa Overdue', 'count' => $overdueTermins, 'severity' => 'danger'];
        }

        // 4. High-priority facility tickets unresolved
        $highFacility = FacilityTicket::where('priority', 'high')
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        if ($highFacility > 0) {
            $alerts[] = ['type' => 'facility_high', 'label' => 'Tiket Fasilitas Prioritas Tinggi', 'count' => $highFacility, 'severity' => 'danger'];
        }

        // 5. Inventory damaged/unusable
        $damagedInventory = InventoryItem::where('status', 'active')
            ->whereIn('condition_status', ['DAMAGED', 'UNUSABLE'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        if ($damagedInventory > 0) {
            $alerts[] = ['type' => 'inventory_damaged', 'label' => 'Inventaris Rusak / Tidak Pakai', 'count' => $damagedInventory, 'severity' => 'warning'];
        }

        // 6. Active SP3 warning letters (most severe)
        $activeSP3 = EmployeeWarningLetter::where('is_active', true)
            ->where('sp_level', 'SP3')
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        if ($activeSP3 > 0) {
            $alerts[] = ['type' => 'sp3_active', 'label' => 'SP3 Aktif', 'count' => $activeSP3, 'severity' => 'danger'];
        }

        // 7. Attendance compliance violations this month
        $violationsThisMonth = AttendanceRuleViolation::whereMonth('period_start_date', $now->month)
            ->whereYear('period_start_date', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        if ($violationsThisMonth > 0) {
            $alerts[] = ['type' => 'attendance_violation', 'label' => 'Pelanggaran Kehadiran Bulan Ini', 'count' => $violationsThisMonth, 'severity' => 'warning'];
        }

        $dangerCount = collect($alerts)->where('severity', 'danger')->count();
        $warningCount = collect($alerts)->where('severity', 'warning')->count();

        return [
            'alerts' => $alerts,
            'total_alerts' => count($alerts),
            'danger_count' => $dangerCount,
            'warning_count' => $warningCount,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        return [];
    }
}
