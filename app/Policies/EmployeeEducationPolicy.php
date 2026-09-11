<?php

namespace App\Policies;

use App\Models\EmployeeEducation;
use App\Models\User;

class EmployeeEducationPolicy
{
    public function update(User $user, EmployeeEducation $education): bool
    {
        return $user->id === $education->employee->user_id;
    }

    public function delete(User $user, EmployeeEducation $education): bool
    {
        return $user->id === $education->employee->user_id;
    }
}
