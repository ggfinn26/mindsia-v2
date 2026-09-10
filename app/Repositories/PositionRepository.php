<?php

namespace App\Repositories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;

class PositionRepository
{
    public function all(): Collection
    {
        return Position::with('role')->orderBy('hierarchy_order')->get();
    }

    public function find(int $id): Position
    {
        return Position::with('role', 'permissions')->findOrFail($id);
    }

    public function create(array $data): Position
    {
        return Position::create($data);
    }

    public function update(Position $position, array $data): Position
    {
        $position->update($data);
        return $position;
    }

    public function syncPermissions(Position $position, array $permissionIds): void
    {
        $position->permissions()->sync($permissionIds);
    }

    public function delete(Position $position): void
    {
        $position->delete();
    }
}
