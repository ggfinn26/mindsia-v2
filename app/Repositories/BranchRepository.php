<?php

namespace App\Repositories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;

class BranchRepository
{
    public function all(): Collection
    {
        return Branch::with('area.region')->orderBy('branch_name')->get();
    }

    public function find(int $id): Branch
    {
        return Branch::with('area', 'picEmployee')->findOrFail($id);
    }

    public function byArea(int $areaId): Collection
    {
        return Branch::where('areas_id', $areaId)->orderBy('branch_name')->get();
    }

    public function active(): Collection
    {
        return Branch::where('is_active', true)->orderBy('branch_name')->get();
    }

    public function create(array $data): Branch
    {
        return Branch::create($data);
    }

    public function update(Branch $branch, array $data): Branch
    {
        $branch->update($data);

        return $branch;
    }

    public function toggleActive(Branch $branch): Branch
    {
        $branch->update(['is_active' => ! $branch->is_active]);

        return $branch;
    }

    public function assignPic(Branch $branch, int $employeeId): Branch
    {
        $branch->update(['ma_pic_employee_id' => $employeeId]);

        return $branch;
    }

    public function delete(Branch $branch): void
    {
        if ($branch->employees()->exists()) {
            throw new \RuntimeException('Tidak bisa hapus branch yang masih memiliki employee.');
        }

        $branch->delete();
    }
}
