<?php

namespace App\Repositories;

use App\Models\ClassRoom;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassRoomRepository
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        return ClassRoom::with('program', 'branch', 'tutor', 'memberClasses')
            ->when($filters['branch_id'] ?? null, fn ($q, $v) => $q->where('branch_id', $v))
            ->when($filters['program_id'] ?? null, fn ($q, $v) => $q->where('program_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(15);
    }

    public function findWithDetails(int $id): ClassRoom
    {
        return ClassRoom::with([
            'program', 'branch', 'tutor', 'schedules', 'tutorChangeHistories',
            'tests.memberTestResults.memberClass',
            'memberClasses.memberRegistration.memberData',
            'memberClasses.memberRegistration.certificate',
        ])->findOrFail($id);
    }

    public function create(array $data): ClassRoom
    {
        return ClassRoom::create($data);
    }

    public function update(ClassRoom $classRoom, array $data): ClassRoom
    {
        $classRoom->update($data);

        return $classRoom;
    }

    public function delete(ClassRoom $classRoom): bool
    {
        return $classRoom->delete();
    }

    public function getAvailableTutors(): array
    {
        return Employee::with(['branch:id,branch_name', 'area:id,name'])
            ->whereHas('employmentStatuses', fn ($q) => $q->whereHas(
                'position',
                fn ($q) => $q->whereIn('position_name', ['Official Tutor', 'Regular Tutor'])
            ))
            ->withCount(['tutorClasses' => fn ($q) => $q->where('status', 'active')])
            ->having('tutor_classes_count', '<', 9)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'branch_id', 'area_id'])
            ->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->full_name,
                'branch_id' => $e->branch_id,
                'area_id' => $e->area_id,
                'branch_name' => $e->branch?->branch_name ?? '—',
                'area_name' => $e->area?->name ?? '—',
                'class_count' => $e->tutor_classes_count,
            ])
            ->values()
            ->toArray();
    }
}
