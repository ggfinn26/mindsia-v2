<?php

namespace App\Policies;

use App\Models\BranchTransferRequest;
use App\Models\User;

class BranchTransferPolicy
{
    public function view(User $user, BranchTransferRequest $transfer): bool
    {
        return $user->id === $transfer->employee->user_id || $user->hasRole('board-of-directors');
    }

    public function review(User $user, BranchTransferRequest $transfer): bool
    {
        return $user->hasRole('board-of-directors');
    }
}
