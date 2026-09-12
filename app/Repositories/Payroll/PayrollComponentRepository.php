<?php

namespace App\Repositories\Payroll;

use App\Models\PayrollComponent;
use Illuminate\Database\Eloquent\Collection;

class PayrollComponentRepository
{
    public function all(bool $activeOnly = false): Collection
    {
        return PayrollComponent::when($activeOnly, fn ($q) => $q->active())->get();
    }

    public function allActive(): Collection
    {
        return $this->all(activeOnly: true);
    }

    public function findById(int $id): PayrollComponent
    {
        return PayrollComponent::findOrFail($id);
    }

    public function create(array $data): PayrollComponent
    {
        return PayrollComponent::create($data);
    }

    public function update(PayrollComponent $component, array $data): PayrollComponent
    {
        $component->update($data);

        return $component;
    }

    public function hasPayrollHistory(PayrollComponent $component): bool
    {
        return $component->payrollItems()->exists();
    }
}
