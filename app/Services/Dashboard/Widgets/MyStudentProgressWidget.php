<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberClass;
use App\Models\MemberCurriculumProgress;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyStudentProgressWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_student_progress';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $activeClasses = MemberClass::where('tutor_employee_id', $employeeId)
            ->where('status', 'active')
            ->with('program:id,program_name')
            ->get();

        $classProgress = $activeClasses->map(function ($class) {
            $members = MemberCurriculumProgress::where('member_class_id', $class->id)
                ->selectRaw('member_id, COUNT(*) as total, SUM(is_completed) as completed')
                ->groupBy('member_id')
                ->get();

            $avgProgress = $members->avg(fn ($m) => $m->total > 0
                ? round(($m->completed / $m->total) * 100, 1)
                : 0);

            return [
                'program' => $class->program?->program_name ?? '-',
                'member_count' => $members->count(),
                'avg_progress_percent' => round($avgProgress ?? 0, 1),
            ];
        })->toArray();

        return [
            'classes' => $classProgress,
            'total_active_classes' => $activeClasses->count(),
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        $activeClasses = MemberClass::where('tutor_employee_id', $employeeId)
            ->where('status', 'active')
            ->with('program:id,program_name')
            ->pluck('id', 'program.program_name');

        return MemberCurriculumProgress::whereIn('member_class_id', $activeClasses->values())
            ->with('member:id,full_name', 'curriculumItem:id,item_name')
            ->get()
            ->map(fn ($p) => [
                'member' => $p->member?->full_name ?? '-',
                'item' => $p->curriculumItem?->item_name ?? '-',
                'is_completed' => $p->is_completed ? 'Ya' : 'Tidak',
            ])->toArray();
    }
}
