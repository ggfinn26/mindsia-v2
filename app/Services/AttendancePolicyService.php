<?php

namespace App\Services;

use App\Models\Employee;

class AttendancePolicyService
{
    /**
     * Approver harus punya hierarchy_order lebih kecil (jabatan lebih tinggi) dari pemohon.
     * Jika pemohon adalah HR staff (punya attendance.leave.review), hanya posisi dengan
     * attendance.leave.approve_hr_staff yang bisa approve.
     */
    public function canApproveLeave(Employee $approver, Employee $requester): bool
    {
        if ($approver->id === $requester->id) {
            return false;
        }

        // HR staff leave → butuh approver dengan permission khusus
        if ($requester->user?->can('attendance.leave.review')) {
            return (bool) $approver->user?->can('attendance.leave.approve_hr_staff');
        }

        $approverOrder = $approver->currentStatus?->position?->hierarchy_order;
        $requesterOrder = $requester->currentStatus?->position?->hierarchy_order;

        if ($approverOrder === null || $requesterOrder === null) {
            return false;
        }

        return $approverOrder < $requesterOrder;
    }
}
