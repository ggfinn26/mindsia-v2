<?php

namespace App\Observers;

use App\Models\ClassRoom;
use App\Services\ClassRoomService;

class ClassRoomObserver
{
    public function __construct(private ClassRoomService $service) {}

    public function created(ClassRoom $classRoom): void
    {
        $this->service->generateSchedules($classRoom);
    }

    public function updated(ClassRoom $classRoom): void
    {
        if ($classRoom->isDirty('tutor_id')) {
            $this->recordTutorChange($classRoom);
        }
    }

    private function recordTutorChange(ClassRoom $classRoom): void
    {
        $classRoom->tutorChangeHistories()->create([
            'from_tutor_id' => $classRoom->getOriginal('tutor_id'),
            'to_tutor_id' => $classRoom->tutor_id,
            'changed_by_employee_id' => auth()->user()?->employee_id,
            'changed_at' => now(),
        ]);
    }
}
