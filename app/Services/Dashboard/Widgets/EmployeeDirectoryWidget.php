<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class EmployeeDirectoryWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'employee_directory';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $query = Employee::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $totalActive = (clone $query)->count();

        $byPosition = EmploymentStatus::whereHas('employee', fn ($q) => $q
            ->where('is_active', true)
            ->when($branchId, fn ($bq) => $bq->where('branch_id', $branchId)))
            ->where('is_current', true)
            ->with('position')
            ->get()
            ->groupBy(fn ($s) => $s->position?->position_name ?? 'Unassigned')
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->take(5)
            ->toArray();

        $recentJoins = EmploymentStatus::whereHas('employee', fn ($q) => $q
            ->where('is_active', true)
            ->when($branchId, fn ($bq) => $bq->where('branch_id', $branchId)))
            ->where('is_current', true)
            ->whereMonth('join_date', $now->month)
            ->whereYear('join_date', $now->year)
            ->count();

        $byEmploymentType = EmploymentStatus::whereHas('employee', fn ($q) => $q
            ->where('is_active', true)
            ->when($branchId, fn ($bq) => $bq->where('branch_id', $branchId)))
            ->where('is_current', true)
            ->selectRaw('type_employment, COUNT(*) as count')
            ->groupBy('type_employment')
            ->pluck('count', 'type_employment')
            ->toArray();

        return [
            'total_active' => $totalActive,
            'by_position' => $byPosition,
            'recent_joins' => $recentJoins,
            'by_employment_type' => $byEmploymentType,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employees = Employee::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with(['currentStatus.position', 'branch'])
            ->get();

        return $employees->map(fn ($e) => [
            'employee_code' => $e->employee_code,
            'full_name' => $e->full_name,
            'gender' => $e->gender,
            'email' => $e->email,
            'branch' => $e->branch?->branch_name ?? '-',
            'position' => $e->currentStatus?->position?->position_name ?? '-',
            'employment_type' => $e->currentStatus?->type_employment ?? '-',
            'join_date' => $e->currentStatus?->join_date?->format('Y-m-d') ?? '-',
        ])->toArray();
    }
}
