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

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $activeClasses = MemberClass::whereHas('classRoom', fn ($q) => $q->where('tutor_id', $employeeId))
            ->where('status', 'active')
            ->with(['classRoom.program', 'curriculumProgress', 'memberRegistration.memberData'])
            ->get();

        $allMembersProgress = [];
        $classProgress = $activeClasses->groupBy('class_id')->map(function ($members) use (&$allMembersProgress) {
            $first = $members->first();
            $classAvg = $members->avg(function ($mc) use (&$allMembersProgress) {
                $total = $mc->curriculumProgress->count();
                $completed = $mc->curriculumProgress->where('status', 'completed')->count();
                $pct = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
                $allMembersProgress[] = $pct;

                return $pct;
            });

            return [
                'program' => $first->classRoom?->program?->program_name ?? '-',
                'class_name' => $first->classRoom?->class_name ?? '-',
                'member_count' => $members->count(),
                'avg_progress_percent' => round($classAvg ?? 0, 1),
            ];
        })->values()->toArray();

        $totalStudents = count($allMembersProgress);
        $onTrack = count(array_filter($allMembersProgress, fn ($pct) => $pct >= 50));
        $behind = $totalStudents - $onTrack;
        $avgRate = $totalStudents > 0 ? round(array_sum($allMembersProgress) / $totalStudents, 1) : 0;

        return [
            'total_students' => $totalStudents,
            'on_track' => $onTrack,
            'behind' => $behind,
            'avg_completion_rate' => $avgRate,
            'classes' => $classProgress,
            'total_active_classes' => $activeClasses->pluck('class_id')->unique()->count(),
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

        $activeClassIds = MemberClass::whereHas('classRoom', fn ($q) => $q->where('tutor_id', $employeeId))
            ->where('status', 'active')
            ->pluck('id');

        return MemberCurriculumProgress::whereIn('member_class_id', $activeClassIds)
            ->with(['memberClass.memberRegistration.memberData', 'curriculumItem:id,item_name'])
            ->get()
            ->map(fn ($p) => [
                'member' => $p->memberClass?->memberRegistration?->memberData?->full_name ?? '-',
                'item' => $p->curriculumItem?->item_name ?? '-',
                'is_completed' => $p->status === 'completed' ? 'Ya' : 'Tidak',
            ])->toArray();
    }
}
