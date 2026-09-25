<?php

namespace App\Services\Alerts;

use App\Models\AttendanceRuleViolation;
use App\Models\EmployeeWarningLetter;
use App\Models\EmploymentStatus;
use Carbon\Carbon;

class EmployeeAlertProvider implements AlertProvider
{
    public function alertsFor(?int $branchId): array
    {
        $alerts = [];

        // SP3 active → danger (sp_level is integer: 1=SP1, 2=SP2, 3=SP3)
        $sp3List = EmployeeWarningLetter::query()
            ->with('employee:id,full_name,branch_id', 'employee.branch:id,branch_name')
            ->where('is_active', true)
            ->where('sp_level', 3)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->get();

        foreach ($sp3List as $sp) {
            $alerts[] = new StrategicAlert(
                moduleKey: 'employee',
                moduleLabel: 'SDM',
                category: 'SP3 Aktif',
                subject: $sp->employee?->full_name ?? '-',
                branchName: $sp->employee?->branch?->branch_name ?? '-',
                detail: 'Surat Peringatan Ketiga masih aktif',
                url: route('employment-statuses.index'),
                severity: 'danger',
                occurredAt: $sp->issued_at ? Carbon::parse($sp->issued_at) : null,
            );
        }

        // Employee contracts expiring ≤ 30 days → warning
        $expiringContracts = EmploymentStatus::query()
            ->with('employee:id,full_name,branch_id', 'employee.branch:id,branch_name')
            ->whereNotNull('contract_end_date')
            ->whereBetween('contract_end_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->whereDoesntHave('offBoarding')
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->get();

        foreach ($expiringContracts as $status) {
            $daysLeft = (int) now()->startOfDay()->diffInDays(Carbon::parse($status->contract_end_date)->startOfDay(), false);
            $alerts[] = new StrategicAlert(
                moduleKey: 'employee',
                moduleLabel: 'SDM',
                category: 'Kontrak Karyawan Segera Berakhir',
                subject: $status->employee?->full_name ?? '-',
                branchName: $status->employee?->branch?->branch_name ?? '-',
                detail: "Sisa {$daysLeft} hari",
                url: route('employment-statuses.index'),
                severity: 'warning',
                occurredAt: Carbon::parse($status->contract_end_date),
            );
        }

        // Attendance violations this month → warning (aggregated per employee)
        $violations = AttendanceRuleViolation::query()
            ->with('employee:id,full_name,branch_id', 'employee.branch:id,branch_name', 'rule:id,rule_name')
            ->whereMonth('period_start_date', now()->month)
            ->whereYear('period_start_date', now()->year)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->get();

        foreach ($violations as $violation) {
            $alerts[] = new StrategicAlert(
                moduleKey: 'employee',
                moduleLabel: 'SDM',
                category: 'Pelanggaran Kehadiran',
                subject: $violation->employee?->full_name ?? '-',
                branchName: $violation->employee?->branch?->branch_name ?? '-',
                detail: $violation->rule?->rule_name ?? 'Pelanggaran bulan ini',
                url: route('employment-statuses.index'),
                severity: 'warning',
                occurredAt: $violation->period_start_date ? Carbon::parse($violation->period_start_date) : null,
            );
        }

        return $alerts;
    }
}
