<?php

namespace App\Policies;

use App\Models\ResignRequest;
use App\Models\User;

class ResignRequestPolicy
{
    public function approve(User $user, ResignRequest $resignRequest): bool
    {
        return $user->hasRole('board-of-directors');
    }

    public function reject(User $user, ResignRequest $resignRequest): bool
    {
        return $user->hasRole('board-of-directors');
    }
}
