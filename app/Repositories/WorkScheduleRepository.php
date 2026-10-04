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

    /** True jika employee punya assignment aktif (tanpa cek hari kerja). */
    public function hasAnyActiveAssignment(int $employeeId, string $date): bool
    {
        $employee = Employee::with(['currentStatus.position'])->find($employeeId);
        if (! $employee) {
            return false;
        }

        $positionId = $employee->currentStatus?->position_id;
        $roleId = $employee->currentStatus?->position?->role_id;

        foreach ([
            ['App\Models\Employee', $employeeId],
            $positionId ? ['App\Models\Position', $positionId] : null,
            $roleId ? [Role::class, $roleId] : null,
        ] as $pair) {
            if (! $pair) {
                continue;
            }
            [$type, $id] = $pair;
            $exists = WorkScheduleAssignment::where('assignable_type', $type)
                ->where('assignable_id', $id)
                ->where('is_active', true)
                ->where('effective_start_date', '<=', $date)
                ->where(fn ($q) => $q->whereNull('effective_end_date')->orWhere('effective_end_date', '>=', $date))
                ->exists();
            if ($exists) {
                return true;
            }
        }

        return false;
    }

    private function findActiveAssignment(string $type, int $id, string $date): ?WorkScheduleAssignment
    {
        $assignment = WorkScheduleAssignment::where('assignable_type', $type)
            ->where('assignable_id', $id)
            ->where('is_active', true)
            ->where('effective_start_date', '<=', $date)
            ->where(fn ($q) => $q->whereNull('effective_end_date')->orWhere('effective_end_date', '>=', $date))
            ->with('workScheduleRule.workDays')
            ->first();

        if ($assignment && ! $assignment->workScheduleRule?->isActiveOnDate($date)) {
            return null;
        }

        return $assignment;
    }
}
