<?php

namespace App\Repositories\Finance;

use App\Models\BudgetEstimate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BudgetEstimateRepository
{
    public function forEmployee(int $employeeId, ?int $year = null, ?int $month = null): LengthAwarePaginator
    {
        return BudgetEstimate::with('branch')
            ->where('submitted_by_employee_id', $employeeId)
            ->when($year, fn ($q) => $q->where('period_year', $year))
            ->when($month, fn ($q) => $q->where('period_month', $month))
            ->latest()
            ->paginate(25);
    }

    public function forReview(string $status): LengthAwarePaginator
    {
        return BudgetEstimate::with(['branch', 'submittedBy'])
            ->where('status', $status)
            ->latest()
            ->paginate(25);
    }

    public function create(array $data): BudgetEstimate
    {
        return BudgetEstimate::create($data);
    }

    public function update(BudgetEstimate $estimate, array $data): void
    {
        $estimate->update($data);
    }

    public function submitOps(BudgetEstimate $estimate): void
    {
        abort_unless($estimate->isDraft(), 422, 'Hanya draft yang bisa diajukan.');
        $estimate->update(['status' => 'ops_review']);
    }

    public function submitFinance(BudgetEstimate $estimate, int $opsEmployeeId): void
    {
        abort_unless($estimate->status === 'ops_review', 422, 'Status tidak valid.');
        $estimate->update([
            'status' => 'finance_review',
            'ops_reviewed_by' => $opsEmployeeId,
            'ops_reviewed_at' => now(),
        ]);
    }

    public function accept(BudgetEstimate $estimate, int $financeEmployeeId): void
    {
        abort_unless($estimate->status === 'finance_review', 422, 'Status tidak valid.');
        $estimate->update([
            'status' => 'accepted',
            'finance_reviewed_by' => $financeEmployeeId,
            'finance_reviewed_at' => now(),
            'rejection_notes' => null,
        ]);
    }

    public function reject(BudgetEstimate $estimate, string $notes): void
    {
        abort_if(empty($notes), 422, 'Alasan penolakan wajib diisi.');
        $estimate->update(['status' => 'draft', 'rejection_notes' => $notes]);
    }

    public function send(BudgetEstimate $estimate, int $hrpEmployeeId): void
    {
        abort_unless($estimate->status === 'accepted', 422, 'Status tidak valid.');

        DB::transaction(function () use ($estimate, $hrpEmployeeId) {
            $estimate->update([
                'status' => 'sent',
                'sent_by' => $hrpEmployeeId,
                'sent_at' => now(),
            ]);

            // auto-generate inventory_draft: update status only (no separate table per flow decision)
            $estimate->update(['status' => 'inventory_draft']);
        });
    }
}
