<?php

namespace App\Repositories\Facility;

use App\Models\BranchRentTermin;
use Illuminate\Database\Eloquent\Collection;

class BranchRentTerminRepository
{
    public function find(int $id): BranchRentTermin
    {
        return BranchRentTermin::with(['contract.branch', 'paidBy'])->findOrFail($id);
    }

    public function markPaid(BranchRentTermin $termin, int $employeeId): BranchRentTermin
    {
        $termin->update([
            'status' => 'paid',
            'paid_at' => today(),
            'paid_by_employee_id' => $employeeId,
        ]);

        return $termin;
    }

    public function markOverdue(BranchRentTermin $termin): void
    {
        $termin->update(['status' => 'overdue']);
    }

    public function unpaidOverdue(): Collection
    {
        return BranchRentTermin::where('status', 'unpaid')
            ->where('due_date', '<', today())
            ->with('contract.branch')
            ->get();
    }

    public function dueSoon(int $daysAhead = 7): Collection
    {
        return BranchRentTermin::where('status', 'unpaid')
            ->whereBetween('due_date', [today()->addDay(), today()->addDays($daysAhead)])
            ->with('contract.branch')
            ->get();
    }
}
