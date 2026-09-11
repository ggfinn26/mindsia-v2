<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('employees.view');
    }

    public function view(User $user, Employee $employee): bool
    {
        if (! $user->can('employees.view')) {
            return false;
        }

        return $this->isInUserScope($user, $employee);
    }

    public function create(User $user): bool
    {
        return $user->can('employees.create');
    }

    public function update(User $user, Employee $employee): bool
    {
        if (! $user->can('employees.update')) {
            return false;
        }

        return $this->isInUserScope($user, $employee);
    }

    public function delete(User $user, Employee $employee): bool
    {
        if (! $user->can('employees.delete')) {
            return false;
        }

        return $this->isInUserScope($user, $employee);
    }

    public function requestBranchTransfer(User $user, Employee $employee): bool
    {
        return $user->id === $employee->user_id;
    }

    public function reviewBranchTransfer(User $user): bool
    {
        return $user->hasRole('board-of-directors');
    }

    public function directBranchTransfer(User $user, Employee $employee): bool
    {
        if (! $user->can('employees.update')) {
            return false;
        }

        return $this->isInUserScope($user, $employee);
    }

    private function isInUserScope(User $user, Employee $employee): bool
    {
        return Employee::inUserScope($user)->where('id', $employee->id)->exists();
    }
}
