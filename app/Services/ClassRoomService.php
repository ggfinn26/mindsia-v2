<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\CurriculumItem;
use App\Models\MemberClass;
use App\Models\MemberCurriculumProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClassRoomService
{
    public function enrollMember(ClassRoom $classRoom, int $memberRegistrationId, string $startDate): MemberClass
    {
        return DB::transaction(function () use ($classRoom, $memberRegistrationId, $startDate) {
            $memberClass = MemberClass::updateOrCreate(
                ['member_registration_id' => $memberRegistrationId, 'class_id' => $classRoom->id],
                ['status' => 'active', 'start_date' => $startDate]
            );

            $curriculum = $classRoom->program->curriculum;
            if ($curriculum) {
                $items = CurriculumItem::whereHas('session', fn ($q) => $q->where('curriculum_id', $curriculum->id))
                    ->where('is_active', true)
                    ->get();

                $progress = $items->map(fn ($item) => [
                    'member_class_id' => $memberClass->id,
                    'curriculum_item_id' => $item->id,
                    'status' => 'not_started',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                MemberCurriculumProgress::insertOrIgnore($progress->toArray());
            }

            return $memberClass;
        });
    }

    public function removeMember(MemberClass $memberClass): bool
    {
        return DB::transaction(function () use ($memberClass) {
            MemberCurriculumProgress::where('member_class_id', $memberClass->id)->delete();

            return $memberClass->delete();
        });
    }

    public function transferMember(MemberClass $oldEnrollment, ClassRoom $newClass): MemberClass
    {
        return DB::transaction(function () use ($oldEnrollment, $newClass) {
            $oldEnrollment->update(['status' => 'inactive']);

            return $this->enrollMember($newClass, $oldEnrollment->member_registration_id, now()->toDateString());
        });
    }

    public function generateSchedules(ClassRoom $classRoom): void
    {
        $now = now();
        $dayMap = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];
        $primaryDay = $dayMap[$classRoom->day_of_week];
        $secondaryDay = $dayMap[ClassRoom::DAY_PAIRS[$classRoom->day_of_week]];

        $startDate = Carbon::parse($classRoom->start_date);
        $schedules = [];

        for ($week = 0; $week < $classRoom->week_count; $week++) {
            $primaryOffset = (7 + $primaryDay - $startDate->dayOfWeek) % 7;
            $primaryDate = $startDate->copy()->addWeeks($week)->addDays($primaryOffset);

            $secondaryOffset = (7 + $secondaryDay - $startDate->dayOfWeek) % 7;
            $secondaryDate = $startDate->copy()->addWeeks($week)->addDays($secondaryOffset);

            $schedules[] = [
                'class_id' => $classRoom->id,
                'schedule_date' => $primaryDate->toDateString(),
                'start_time' => $classRoom->start_time_primary,
                'end_time' => $classRoom->end_time_primary,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $schedules[] = [
                'class_id' => $classRoom->id,
                'schedule_date' => $secondaryDate->toDateString(),
                'start_time' => $classRoom->start_time_secondary ?? $classRoom->start_time_primary,
                'end_time' => $classRoom->end_time_secondary ?? $classRoom->end_time_primary,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        ClassSchedule::insert($schedules);
    }
}
