<?php

namespace App\Repositories\Finance;

use App\Models\Reimbursement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReimbursementRepository
{
    public function forEmployee(int $employeeId): LengthAwarePaginator
    {
        return Reimbursement::with('branch')
            ->where('employee_id', $employeeId)
            ->latest()
            ->paginate(25);
    }

    public function forReview(): LengthAwarePaginator
    {
        return Reimbursement::with(['employee', 'branch'])
            ->where('status', 'PENDING_REVIEW')
            ->latest()
            ->paginate(25);
    }

    public function create(array $data): Reimbursement
    {
        return Reimbursement::create($data);
    }

    public function update(Reimbursement $reimbursement, array $data): void
    {
        abort_unless($reimbursement->isDraft(), 422, 'Hanya draft yang bisa diedit.');
        $reimbursement->update($data);
    }

    public function submit(Reimbursement $reimbursement): void
    {
        abort_unless($reimbursement->isDraft(), 422, 'Hanya draft yang bisa diajukan.');
        $reimbursement->update(['status' => 'PENDING_REVIEW']);
    }

    public function approve(Reimbursement $reimbursement, int $reviewerEmployeeId, ?string $notes): void
    {
        abort_unless($reimbursement->status === 'PENDING_REVIEW', 422, 'Status tidak valid.');
        $reimbursement->update([
            'status' => 'APPROVED',
            'reviewed_by_employee_id' => $reviewerEmployeeId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    public function reject(Reimbursement $reimbursement, int $reviewerEmployeeId, string $notes): void
    {
        abort_unless($reimbursement->status === 'PENDING_REVIEW', 422, 'Status tidak valid.');
        $reimbursement->update([
            'status' => 'REJECTED',
            'reviewed_by_employee_id' => $reviewerEmployeeId,
            'reviewed_at' => now(),
            'rejected_at' => now(),
            'rejection_notes' => $notes,
        ]);
    }

    public function revertToDraft(Reimbursement $reimbursement): void
    {
        abort_unless($reimbursement->status === 'REJECTED', 422, 'Hanya reimbursement rejected yang bisa dikembalikan ke draft.');
        $reimbursement->update([
            'status' => 'DRAFT',
            'reviewed_by_employee_id' => null,
            'reviewed_at' => null,
            'rejected_at' => null,
            'rejection_notes' => null,
        ]);
    }

    public function markPaid(Reimbursement $reimbursement, int $employeeId): void
    {
        abort_unless($reimbursement->status === 'APPROVED', 422, 'Hanya reimbursement approved yang bisa ditandai paid.');
        $reimbursement->update([
            'status' => 'PAID',
            'paid_by_employee_id' => $employeeId,
            'paid_at' => now(),
        ]);
    }
}
