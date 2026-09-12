<?php

namespace App\Repositories;

use App\Models\ClassRoom;
use App\Models\Employee;
use Illuminate\Pagination\Paginator;

class ClassRoomRepository
{
    public function all(): Paginator
    {
        return ClassRoom::with('program', 'branch', 'tutor')
            ->latest()
            ->paginate(15);
    }

    public function findWithDetails(int $id): ClassRoom
    {
        return ClassRoom::with('program', 'branch', 'tutor', 'schedules', 'memberClasses', 'tutorChangeHistories')
            ->findOrFail($id);
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
