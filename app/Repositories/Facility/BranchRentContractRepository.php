<?php

namespace App\Repositories\Facility;

use App\Models\BranchRentContract;
use Illuminate\Database\Eloquent\Collection;

class BranchRentContractRepository
{
    public function find(int $id): BranchRentContract
    {
        return BranchRentContract::with(['branch', 'area', 'createdBy', 'termins'])->findOrFail($id);
    }

    public function byBranch(int $branchId, ?string $status = null): Collection
    {
        return BranchRentContract::where('branch_id', $branchId)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('start_date')
            ->get();
    }

    public function create(array $data): BranchRentContract
    {
        return BranchRentContract::create($data);
    }

    public function update(BranchRentContract $contract, array $data): BranchRentContract
    {
        $contract->update($data);

        return $contract;
    }

    public function delete(BranchRentContract $contract): void
    {
        $contract->delete();
    }
}
