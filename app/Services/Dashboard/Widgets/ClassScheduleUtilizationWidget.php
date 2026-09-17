<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\BranchProgramQuota;
use App\Models\ClassRoom;
use App\Models\MemberClass;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class ClassScheduleUtilizationWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'class_schedule_utilization';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $classesQuery = ClassRoom::when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $totalClasses = (clone $classesQuery)->count();
        $activeClasses = (clone $classesQuery)->where('status', 'active')->count();
        $completedClasses = (clone $classesQuery)->where('status', 'completed')->count();
        $plannedClasses = (clone $classesQuery)->where('status', 'planned')->count();

        $activeMemberClasses = MemberClass::where('status', 'active')
            ->when($branchId, fn ($q) => $q->whereHas('classRoom', fn ($c) => $c->where('branch_id', $branchId)))
            ->count();

        $totalQuota = BranchProgramQuota::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('quota_limit');

        $utilization = $totalQuota > 0
            ? round(($activeMemberClasses / $totalQuota) * 100, 1)
            : 0;

        return [
            'total_classes' => $totalClasses,
            'active_classes' => $activeClasses,
            'completed_classes' => $completedClasses,
            'planned_classes' => $plannedClasses,
            'active_members' => $activeMemberClasses,
            'total_quota' => $totalQuota,
            'utilization_percent' => $utilization,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $classes = ClassRoom::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with(['program', 'memberClasses'])
            ->get();

        return $classes->map(fn ($c) => [
            'class_name' => $c->class_name,
            'program' => $c->program?->program_name ?? '-',
            'status' => $c->status,
            'day' => $c->day_of_week,
            'members' => $c->memberClasses->count(),
            'start_date' => $c->start_date?->format('Y-m-d'),
            'end_date' => $c->end_date?->format('Y-m-d'),
        ])->toArray();
    }
}
