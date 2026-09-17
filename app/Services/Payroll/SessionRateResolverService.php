<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\SessionCompensationRule;

class SessionRateResolverService
{
    public function resolve(Employee $employee): float
    {
        return (float) ($this->resolveRule($employee)?->amount_per_session ?? 0.0);
    }

    public function resolveRule(Employee $employee): ?SessionCompensationRule
    {
        $roleId = $employee->user?->roles->first()?->id;
        $positionId = $employee->currentStatus?->position_id;

        return SessionCompensationRule::active()
            ->where(function ($q) use ($employee, $positionId, $roleId) {
                $q->where(fn ($q) => $q->where('scope_type', 'employee')->where('employee_id', $employee->id))
                    ->orWhere(fn ($q) => $q->where('scope_type', 'position')->where('position_id', $positionId))
                    ->orWhere(fn ($q) => $q->where('scope_type', 'role')->where('role_id', $roleId))
                    ->orWhere('scope_type', 'global');
            })
            ->orderByRaw("FIELD(scope_type, 'employee', 'position', 'role', 'global')")
            ->first();
    }
}
