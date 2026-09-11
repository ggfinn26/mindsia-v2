<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\MemberClass;
use App\Models\MemberCurriculumProgress;
use App\Models\CurriculumItem;
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
                $items = CurriculumItem::whereHas('session', fn($q) => $q->where('curriculum_id', $curriculum->id))
                    ->where('is_active', true)
                    ->get();

                $progress = $items->map(fn($item) => [
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
}
