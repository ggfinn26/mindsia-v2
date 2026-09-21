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
            'program', 'branch', 'tutor', 'schedules', 'tutorChangeHistories', 'tests',
            'memberClasses.registration.memberData',
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
        return Employee::whereHas('tutorClasses', fn ($q) => $q->whereIn('status', ['planned', 'active']))
            ->withCount(['tutorClasses' => fn ($q) => $q->whereIn('status', ['planned', 'active'])])
            ->having('tutor_classes_count', '<', 9)
            ->pluck('full_name', 'id')
            ->toArray();
    }
}
