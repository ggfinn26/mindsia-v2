<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberClass;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyClassesWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_classes';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $classes = MemberClass::where('tutor_employee_id', $employeeId)
            ->with('program:id,program_name', 'classRoom:id,room_name,branch_id')
            ->get();

        $byStatus = $classes->groupBy('status')
            ->map(fn ($g) => $g->count())
            ->toArray();

        $active = $classes->where('status', 'active')->map(fn ($c) => [
            'program' => $c->program?->program_name ?? '-',
            'room' => $c->classRoom?->room_name ?? '-',
            'status' => $c->status,
        ])->values()->toArray();

        return [
            'by_status' => $byStatus,
            'active_classes' => $active,
            'total_active' => $byStatus['active'] ?? 0,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return MemberClass::where('tutor_employee_id', $employeeId)
            ->with('program:id,program_name')
            ->get()
            ->map(fn ($c) => [
                'program' => $c->program?->program_name ?? '-',
                'status' => $c->status,
                'start_date' => $c->start_date?->format('Y-m-d'),
            ])->toArray();
    }
}
