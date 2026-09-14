<?php

namespace App\Repositories\Finance;

use App\Models\BranchMonthlyCost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BranchMonthlyCostRepository
{
    public function list(int $branchId, int $year, int $month): LengthAwarePaginator
    {
        return BranchMonthlyCost::with('recordedBy')
            ->where('branch_id', $branchId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->orderBy('category')
            ->paginate(25);
    }

    public function create(array $data): BranchMonthlyCost
    {
        return BranchMonthlyCost::create($data);
    }

    public function update(BranchMonthlyCost $cost, array $data): void
    {
        $cost->update($data);
    }

    public function delete(BranchMonthlyCost $cost): void
    {
        $cost->delete();
    }
}
