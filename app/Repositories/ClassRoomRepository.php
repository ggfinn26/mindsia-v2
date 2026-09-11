<?php

namespace App\Repositories;

use App\Models\ClassRoom;
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
        return $classRoom->fresh();
    }

    public function delete(ClassRoom $classRoom): bool
    {
        return $classRoom->delete();
    }

    public function getAvailableTutors(): array
    {
        return \DB::table('employees')
            ->whereHas('tutorClasses', fn($q) => $q->whereIn('status', ['planned', 'active']))
            ->selectRaw('employees.id, COUNT(classes.id) as active_class_count')
            ->leftJoin('classes', 'employees.id', '=', 'classes.tutor_id')
            ->groupBy('employees.id')
            ->having('active_class_count', '<', 9)
            ->get()
            ->mapWithKeys(fn($t) => [$t->id => $t->name])
            ->toArray();
    }
}
