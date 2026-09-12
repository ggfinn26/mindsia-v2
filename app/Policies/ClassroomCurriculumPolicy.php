<?php

namespace App\Policies;

use App\Models\ClassRoom;
use App\Models\CurriculumItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassroomCurriculumPolicy
{
    use HandlesAuthorization;

    public function viewCurriculum(User $user, ClassRoom $classroom): bool
    {
        if (! $user->employee) {
            return false;
        }

        $employee = $user->employee;

        if ($employee->id === $classroom->tutor_id) {
            return true;
        }

        return $classroom->memberClasses()
            ->whereRelation('memberRegistration', 'employee_id', $employee->id)
            ->exists();
    }

    public function viewItem(User $user, ClassRoom $classroom, CurriculumItem $item): bool
    {
        if (! $this->viewCurriculum($user, $classroom)) {
            return false;
        }

        return $item->session->curriculum_id === $classroom->program->curriculum_id;
    }
}
