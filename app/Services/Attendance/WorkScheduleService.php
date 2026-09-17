<?php

namespace App\Services\Attendance;

use App\Models\Employee;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use Carbon\Carbon;

class WorkScheduleService
{
    public function getCurrentScheduleForEmployee(Employee $employee, ?Carbon $date = null): ?WorkScheduleRule
    {
        $date ??= today();

        $assignment = WorkScheduleAssignment::where('assignable_type', 'employee')
            ->where('assignable_id', $employee->id)
            ->where('is_active', true)
            ->where('effective_start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_end_date')
                    ->orWhere('effective_end_date', '>=', $date);
            })
            ->latest('effective_start_date')
            ->with('workScheduleRule')
            ->first();

        if ($assignment) {
            return $assignment->workScheduleRule;
        }

        $position = $employee->currentStatus?->position;
        if ($position) {
            $assignment = WorkScheduleAssignment::where('assignable_type', 'position')
                ->where('assignable_id', $position->id)
                ->where('is_active', true)
                ->where('effective_start_date', '<=', $date)
                ->where(function ($q) use ($date) {
                    $q->whereNull('effective_end_date')
                        ->orWhere('effective_end_date', '>=', $date);
                })
                ->latest('effective_start_date')
                ->with('workScheduleRule')
                ->first();

            if ($assignment) {
                return $assignment->workScheduleRule;
            }
        }

        $user = $employee->user;
        if ($user) {
            $roleIds = $user->roles->pluck('id');
            if ($roleIds->isNotEmpty()) {
                $assignment = WorkScheduleAssignment::where('assignable_type', 'role')
                    ->whereIn('assignable_id', $roleIds)
                    ->where('is_active', true)
                    ->where('effective_start_date', '<=', $date)
                    ->where(function ($q) use ($date) {
                        $q->whereNull('effective_end_date')
                            ->orWhere('effective_end_date', '>=', $date);
                    })
                    ->latest('effective_start_date')
                    ->with('workScheduleRule')
                    ->first();

                if ($assignment) {
                    return $assignment->workScheduleRule;
                }
            }
        }

        return null;
    }

    public function assignScheduleToEmployee(Employee $employee, WorkScheduleRule $rule, Carbon $effectiveStartDate, ?Carbon $effectiveEndDate = null): WorkScheduleAssignment
    {
        return WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id,
            'assignable_type' => 'employee',
            'assignable_id' => $employee->id,
            'effective_start_date' => $effectiveStartDate,
            'effective_end_date' => $effectiveEndDate,
            'is_active' => true,
        ]);
    }
}
