<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\AttendancePolicy;
use App\Models\AttendanceRuleViolation;
use App\Models\EmployeeWarningLetter;
use App\Models\SopDocument;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class CompliancePoliciesWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'compliance_policies';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $sopQuery = SopDocument::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $activeSopCount = (clone $sopQuery)->count();

        $sopByCategory = (clone $sopQuery)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $policyQuery = AttendancePolicy::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $activePolicies = (clone $policyQuery)->count();

        $exemptPolicies = (clone $policyQuery)->where('is_attendance_exempt', true)->count();

        $violationsThisMonth = AttendanceRuleViolation::whereMonth('period_start_date', $currentMonth)
            ->whereYear('period_start_date', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        $activeWarningLetters = EmployeeWarningLetter::where('is_active', true)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        $bySpLevel = EmployeeWarningLetter::where('is_active', true)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->selectRaw('sp_level, COUNT(*) as count')
            ->groupBy('sp_level')
            ->pluck('count', 'sp_level')
            ->toArray();

        return [
            'active_sop_count' => $activeSopCount,
            'sop_by_category' => $sopByCategory,
            'active_policies' => $activePolicies,
            'exempt_policies' => $exemptPolicies,
            'violations_this_month' => $violationsThisMonth,
            'active_warning_letters' => $activeWarningLetters,
            'by_sp_level' => $bySpLevel,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $sops = SopDocument::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get();

        return $sops->map(fn ($s) => [
            'document_code' => $s->document_code,
            'title' => $s->title,
            'category' => $s->category,
            'version' => $s->version,
            'effective_date' => $s->effective_date?->format('Y-m-d') ?? '-',
        ])->toArray();
    }
}
