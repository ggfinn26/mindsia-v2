<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User;

class EmployeeRepository
{
    public function paginate(array $filters = [], ?User $user = null): LengthAwarePaginator
    {
        return $this->applyFilters(Employee::with('currentStatus.position', 'region', 'branch'), $filters, $user)
            ->orderBy('full_name')
            ->paginate(25);
    }

    private function applyFilters(Builder $query, array $filters, ?User $user = null): Builder
    {
        $query->inUserScope($user);

        return $query
            ->when($filters['region_id'] ?? null, fn ($q, $v) => $q->where('region_id', $v))
            ->when($filters['branch_id'] ?? null, fn ($q, $v) => $q->where('branch_id', $v))
            ->when(isset($filters['is_active']), fn ($q) => $q->where('is_active', $filters['is_active']))
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('full_name', 'like', "%{$v}%")
                    ->orWhere('employee_code', 'like', "%{$v}%");
            }));
    }

    public function find(int $id, ?User $user = null): Employee
    {
        return Employee::with(
            'currentStatus.position.role',
            'employmentStatuses.position',
            'educations',
            'workHistories',
            'branch',
            'region',
            'user',
        )
            ->inUserScope($user)
            ->findOrFail($id);
    }

    public function findByCode(string $code): Employee
    {
        return Employee::where('employee_code', $code)->firstOrFail();
    }

    public function active(): Collection
    {
        return Employee::active()->with('currentStatus.position')->orderBy('full_name')->get();
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);

        return $employee;
    }

    public function deactivate(Employee $employee): Employee
    {
        $employee->update(['is_active' => false]);

        return $employee;
    }
}
