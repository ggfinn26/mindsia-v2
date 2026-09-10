<?php

namespace App\Repositories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Collection;

class AreaRepository
{
    public function all(): Collection
    {
        return Area::with('region.province')->orderBy('name')->get();
    }

    public function find(int $id): Area
    {
        return Area::findOrFail($id);
    }

    public function byRegion(int $regionId): Collection
    {
        return Area::where('region_id', $regionId)->orderBy('name')->get();
    }

    public function create(array $data): Area
    {
        return Area::create($data);
    }

    public function update(Area $area, array $data): Area
    {
        $area->update($data);

        return $area;
    }

    public function delete(Area $area): void
    {
        if ($area->branches()->where('is_active', true)->exists()) {
            throw new \RuntimeException('Tidak bisa hapus area yang masih memiliki branch aktif.');
        }

        $area->delete();
    }
}
