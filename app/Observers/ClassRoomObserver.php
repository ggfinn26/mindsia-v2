<?php

namespace App\Observers;

use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use Carbon\Carbon;

class ClassRoomObserver
{
    public function created(ClassRoom $classRoom): void
    {
        $this->generateSchedules($classRoom);
    }

    public function updated(ClassRoom $classRoom): void
    {
        if ($classRoom->isDirty('tutor_id')) {
            $this->recordTutorChange($classRoom);
        }
    }

    private function generateSchedules(ClassRoom $classRoom): void
    {
        $secondary = ClassRoom::DAY_PAIRS[$classRoom->day_of_week];
        $dayMap = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];
        $primaryDay = $dayMap[$classRoom->day_of_week];
        $secondaryDay = $dayMap[$secondary];

        $startDate = Carbon::parse($classRoom->start_date);
        $schedules = [];

        for ($week = 0; $week < $classRoom->week_count; $week++) {
            $primaryDate = $startDate->copy()->addWeeks($week);
            while ($primaryDate->dayOfWeek != $primaryDay) {
                $primaryDate->addDay();
            }

            $secondaryDate = $startDate->copy()->addWeeks($week);
            while ($secondaryDate->dayOfWeek != $secondaryDay) {
                $secondaryDate->addDay();
            }

            $schedules[] = [
                'class_id' => $classRoom->id,
                'schedule_date' => $primaryDate->toDateString(),
                'start_time' => $classRoom->start_time_primary,
                'end_time' => $classRoom->end_time_primary,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $schedules[] = [
                'class_id' => $classRoom->id,
                'schedule_date' => $secondaryDate->toDateString(),
                'start_time' => $classRoom->start_time_secondary ?? $classRoom->start_time_primary,
                'end_time' => $classRoom->end_time_secondary ?? $classRoom->end_time_primary,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        ClassSchedule::insert($schedules);
    }

    private function recordTutorChange(ClassRoom $classRoom): void
    {
        $classRoom->tutorChangeHistories()->create([
            'from_tutor_id' => $classRoom->getOriginal('tutor_id'),
            'to_tutor_id' => $classRoom->tutor_id,
            'changed_by_employee_id' => auth()->id(),
            'changed_at' => now(),
        ]);
    }
}
