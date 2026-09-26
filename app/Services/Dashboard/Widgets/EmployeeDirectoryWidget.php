<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Employee;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class EmployeeDirectoryWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'employee_directory';
    }

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();

        $query = Employee::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $totalActive = (clone $query)->count();

        $activeWithStatus = Employee::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('currentStatus.position')
            ->get();

        $byPosition = $activeWithStatus
            ->groupBy(fn ($e) => $e->currentStatus?->position?->position_name ?? 'Unassigned')
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->take(5)
            ->toArray();

        $recentJoins = $activeWithStatus->filter(fn ($e) => $e->currentStatus
            && $e->currentStatus->join_date
            && $e->currentStatus->join_date->month === $now->month
            && $e->currentStatus->join_date->year === $now->year
        )->count();

        $byEmploymentType = $activeWithStatus
            ->groupBy(fn ($e) => $e->currentStatus?->type_employment ?? 'Lainnya')
            ->map(fn ($g) => $g->count())
            ->toArray();

        return [
            'total_active' => $totalActive,
            'by_position' => $byPosition,
            'recent_joins' => $recentJoins,
            'by_employment_type' => $byEmploymentType,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
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
