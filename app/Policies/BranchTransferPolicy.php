<?php

namespace App\Policies;

use App\Models\BranchTransferRequest;
use App\Models\User;

class BranchTransferPolicy
{
    public function view(User $user, BranchTransferRequest $transfer): bool
    {
        return $user->id === $transfer->employee->user_id || $user->can('contract.manage');
    }

    public function review(User $user, BranchTransferRequest $transfer): bool
    {
        return $user->can('organization.branch_transfer.review');
    }
}
