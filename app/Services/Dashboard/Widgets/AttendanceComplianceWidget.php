<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\AttendanceRuleViolation;
use App\Models\Employee;
use App\Models\EmployeeWarningLetter;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class AttendanceComplianceWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'attendance_compliance';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $activeEmployees = Employee::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $violationsThisMonth = AttendanceRuleViolation::whereMonth('period_start_date', $currentMonth)
            ->whereYear('period_start_date', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        $employeesWithViolations = AttendanceRuleViolation::whereMonth('period_start_date', $currentMonth)
            ->whereYear('period_start_date', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->distinct('employee_id')
            ->count('employee_id');

        $complianceRate = $activeEmployees > 0
            ? round((($activeEmployees - $employeesWithViolations) / $activeEmployees) * 100, 1)
            : 100;

        $activeWarningLetters = EmployeeWarningLetter::where('is_active', true)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        $sp1Count = EmployeeWarningLetter::where('is_active', true)
            ->where('sp_level', 'SP1')
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        $sp2Count = EmployeeWarningLetter::where('is_active', true)
            ->where('sp_level', 'SP2')
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        $sp3Count = EmployeeWarningLetter::where('is_active', true)
            ->where('sp_level', 'SP3')
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        return [
            'compliance_rate' => $complianceRate,
            'total_violations' => $violationsThisMonth,
            'employees_with_violations' => $employeesWithViolations,
            'active_warning_letters' => $activeWarningLetters,
            'sp1_count' => $sp1Count,
            'sp2_count' => $sp2Count,
            'sp3_count' => $sp3Count,
            'active_employees' => $activeEmployees,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $violations = AttendanceRuleViolation::when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->with(['employee', 'rule'])
            ->orderByDesc('period_start_date')
            ->limit(500)
            ->get();

        return $violations->map(fn ($v) => [
            'employee' => $v->employee?->fullname ?? '-',
            'rule' => $v->rule?->rule_name ?? '-',
            'trigger_value' => $v->trigger_value,
            'period_start' => $v->period_start_date?->format('Y-m-d'),
            'period_end' => $v->period_end_date?->format('Y-m-d'),
        ])->toArray();
    }
}
