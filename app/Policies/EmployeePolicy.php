<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('employee.view');
    }

    public function view(User $user, Employee $employee): bool
    {
        if (! $user->can('employee.view')) {
            return false;
        }

        return $this->isInUserScope($user, $employee);
    }

    public function create(User $user): bool
    {
        return $user->can('employee.create');
    }

    public function update(User $user, Employee $employee): bool
    {
        if (! $user->can('employee.update')) {
            return false;
        }

        return $this->isInUserScope($user, $employee);
    }

    public function delete(User $user, Employee $employee): bool
    {
        if (! $user->can('employee.delete')) {
            return false;
        }

        return $this->isInUserScope($user, $employee);
    }

    public function terminateContract(User $user, Employee $employee): bool
    {
        if (! $user->can('contract.manage')) {
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
        return $user->can('organization.branch_transfer.review');
    }

    public function directBranchTransfer(User $user, Employee $employee): bool
    {
        if (! $user->can('employee.update')) {
            return false;
        }

        return $this->isInUserScope($user, $employee);
    }

    private function isInUserScope(User $user, Employee $employee): bool
    {
        return Employee::inUserScope($user)->where('id', $employee->id)->exists();
    }
}
