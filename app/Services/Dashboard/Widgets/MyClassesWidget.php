<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\ClassRoom;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyClassesWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_classes';
    }

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $classesQuery = ClassRoom::where('tutor_id', $employeeId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('program:id,program_name')
            ->withCount(['memberClasses as active_members_count' => fn ($q) => $q->where('status', 'active')]);

        $classes = $classesQuery->get();

        $activeClasses = $classes->where('status', 'active');
        $totalStudents = (int) $activeClasses->sum('active_members_count');

        $classList = $activeClasses->map(fn ($c) => [
            'name' => $c->class_name,
            'program' => $c->program?->program_name ?? '-',
            'member_count' => (int) $c->active_members_count,
            'status' => $c->status,
        ])->values()->toArray();

        $byStatus = $classes->groupBy('status')
            ->map(fn ($g) => $g->count())
            ->toArray();

        return [
            'total_classes' => $activeClasses->count(),
            'total_students' => $totalStudents,
            'classes' => $classList,
            // Compatibility keys
            'by_status' => $byStatus,
            'active_classes' => $classList,
            'total_active' => $activeClasses->count(),
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return ClassRoom::where('tutor_id', $employeeId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('program:id,program_name')
            ->withCount(['memberClasses as active_members_count' => fn ($q) => $q->where('status', 'active')])
            ->get()
            ->map(fn ($c) => [
                'class_name' => $c->class_name,
                'program' => $c->program?->program_name ?? '-',
                'status' => $c->status,
                'students' => (int) $c->active_members_count,
                'start_date' => $c->start_date?->format('Y-m-d'),
            ])->toArray();
    }
}
