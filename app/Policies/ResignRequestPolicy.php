<?php

namespace App\Policies;

use App\Models\ResignRequest;
use App\Models\User;

class ResignRequestPolicy
{
    public function view(User $user, ResignRequest $resignRequest): bool
    {
        return $user->id === $resignRequest->employee->user_id || $user->can('contract.manage');
    }

    public function approve(User $user, ResignRequest $resignRequest): bool
    {
        return $user->can('contract.manage');
    }

    public function reject(User $user, ResignRequest $resignRequest): bool
    {
        return $user->can('contract.manage');
    }
}
