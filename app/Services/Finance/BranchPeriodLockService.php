<?php

namespace App\Services\Finance;

use App\Models\BranchPeriodLock;
use App\Models\BranchPeriodLockHistory;
use Illuminate\Support\Facades\DB;

class BranchPeriodLockService
{
    public function isLocked(int $branchId, int $year, int $month): bool
    {
        return BranchPeriodLock::where('branch_id', $branchId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->where('is_locked', true)
            ->exists();
    }

    public function assertUnlocked(int $branchId, int $year, int $month): void
    {
        abort_if($this->isLocked($branchId, $year, $month), 422, 'Periode ini sudah dikunci. Hubungi BOARD untuk membuka kunci.');
    }

    public function lock(int $branchId, int $year, int $month, int $employeeId, ?string $notes = null): void
    {
        DB::transaction(function () use ($branchId, $year, $month, $employeeId, $notes) {
            $lock = BranchPeriodLock::updateOrCreate(
                ['branch_id' => $branchId, 'period_year' => $year, 'period_month' => $month],
                ['is_locked' => true, 'locked_by_employee_id' => $employeeId, 'locked_at' => now()]
            );

            BranchPeriodLockHistory::create([
                'branch_period_lock_id' => $lock->id,
                'action' => 'locked',
                'performed_by_employee_id' => $employeeId,
                'performed_at' => now(),
                'notes' => $notes,
            ]);
        });
    }

    public function unlock(int $branchId, int $year, int $month, int $employeeId, ?string $notes = null): void
    {
        DB::transaction(function () use ($branchId, $year, $month, $employeeId, $notes) {
            $lock = BranchPeriodLock::updateOrCreate(
                ['branch_id' => $branchId, 'period_year' => $year, 'period_month' => $month],
                ['is_locked' => false, 'unlocked_by_employee_id' => $employeeId, 'unlocked_at' => now()]
            );

            BranchPeriodLockHistory::create([
                'branch_period_lock_id' => $lock->id,
                'action' => 'unlocked',
                'performed_by_employee_id' => $employeeId,
                'performed_at' => now(),
                'notes' => $notes,
            ]);
        });
    }
}
