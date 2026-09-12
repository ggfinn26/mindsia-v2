<?php

namespace App\Services;

use App\Models\Employee;

class AttendancePolicyService
{
    /**
     * Approver harus punya hierarchy_order lebih kecil (jabatan lebih tinggi) dari pemohon.
     * Jika pemohon adalah HRR/HRP (role), hanya BOARD yang bisa approve.
     */
    public function canApproveLeave(Employee $approver, Employee $requester): bool
    {
        if ($approver->id === $requester->id) {
            return false;
        }

        $requesterRole = $requester->currentStatus?->position?->role?->role_name;

        // HRR/HRP pemohon → hanya BOARD approver
        if (in_array($requesterRole, ['HRR', 'HRP'])) {
            $approverRole = $approver->currentStatus?->position?->role?->role_name;

            return $approverRole === 'BOARD';
        }

        $approverOrder = $approver->currentStatus?->position?->hierarchy_order;
        $requesterOrder = $requester->currentStatus?->position?->hierarchy_order;

        if ($approverOrder === null || $requesterOrder === null) {
            return false;
        }

        return $approverOrder < $requesterOrder;
    }
}
