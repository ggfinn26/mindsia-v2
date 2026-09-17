<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use Spatie\Permission\Models\Role;

class WorkScheduleRepository
{
    /**
     * Resolve active schedule for employee on a date.
     * Priority: employee > position > role.
     */
    public function resolveForEmployee(int $employeeId, string $date): ?WorkScheduleRule
    {
        $employee = Employee::with(['currentStatus.position'])->find($employeeId);

        if (! $employee) {
            return null;
        }

        $positionId = $employee->currentStatus?->position_id;
        $roleId = $employee->currentStatus?->position?->role_id;

        // Employee-level
        $assignment = $this->findActiveAssignment('App\Models\Employee', $employeeId, $date);

        // Position-level fallback
        if (! $assignment && $positionId) {
            $assignment = $this->findActiveAssignment('App\Models\Position', $positionId, $date);
        }

        // Role-level fallback
        if (! $assignment && $roleId) {
            $assignment = $this->findActiveAssignment(Role::class, $roleId, $date);
        }

        return $assignment?->workScheduleRule;
    }

    private function findActiveAssignment(string $type, int $id, string $date): ?WorkScheduleAssignment
    {
        return WorkScheduleAssignment::where('assignable_type', $type)
            ->where('assignable_id', $id)
            ->where('is_active', true)
            ->where('effective_start_date', '<=', $date)
            ->where(fn ($q) => $q->whereNull('effective_end_date')->orWhere('effective_end_date', '>=', $date))
            ->with('workScheduleRule')
            ->first();
    }
}
