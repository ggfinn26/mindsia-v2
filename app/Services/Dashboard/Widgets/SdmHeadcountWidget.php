<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\JobPermintaanDetail;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class SdmHeadcountWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'sdm_headcount';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $activeEmployees = Employee::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $byEmploymentType = EmploymentStatus::whereHas('employee', fn ($q) => $q
            ->where('is_active', true)
            ->when($branchId, fn ($bq) => $bq->where('branch_id', $branchId)))
            ->where('is_current', true)
            ->selectRaw('type_employment, COUNT(*) as count')
            ->groupBy('type_employment')
            ->pluck('count', 'type_employment')
            ->toArray();

        $byGender = Employee::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        $requestedHeadcount = JobPermintaanDetail::whereHas('jobPermintaan', fn ($q) => $q
            ->where('status', 'approved'))
            ->when($branchId, fn ($q) => $q->whereHas('jobPermintaan', fn ($bq) => $bq->where('branch_id', $branchId)))
            ->sum('headcount');

        $prevMonth = $now->copy()->subMonth();
        $prevActiveEmployees = Employee::where('is_active', true)
            ->where('created_at', '<=', $prevMonth->endOfMonth())
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $trend = $prevActiveEmployees > 0
            ? round((($activeEmployees - $prevActiveEmployees) / $prevActiveEmployees) * 100, 1)
            : 0;

        return [
            'active_employees' => $activeEmployees,
            'requested_headcount' => $requestedHeadcount,
            'headcount_gap' => $requestedHeadcount - $activeEmployees,
            'by_employment_type' => $byEmploymentType,
            'by_gender' => $byGender,
            'trend_percent' => $trend,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $employees = Employee::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with(['currentStatus', 'branch'])
            ->get();

        return $employees->map(fn ($e) => [
            'employee_code' => $e->employee_code,
            'full_name' => $e->full_name,
            'gender' => $e->gender,
            'branch' => $e->branch?->branch_name ?? '-',
            'employment_type' => $e->currentStatus?->type_employment ?? '-',
            'join_date' => $e->currentStatus?->join_date?->format('Y-m-d') ?? '-',
        ])->toArray();
    }
}
