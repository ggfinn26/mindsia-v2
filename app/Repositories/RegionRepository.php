<?php

namespace App\Repositories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Collection;

class RegionRepository
{
    public function all(): Collection
    {
        return Region::with('province')->orderBy('name')->get();
    }

    public function find(int $id): Region
    {
        return Region::findOrFail($id);
    }

    public function byProvince(int $provinceId): Collection
    {
        return Region::where('province_id', $provinceId)->orderBy('name')->get();
    }

    public function create(array $data): Region
    {
        return Region::create($data);
    }

    public function update(Region $region, array $data): Region
    {
        $region->update($data);

        return $region;
    }

    public function delete(Region $region): void
    {
        if ($region->areas()->exists() || $region->institutions()->exists()) {
            throw new \RuntimeException('Tidak bisa hapus region yang masih memiliki area atau institusi.');
        }

        $region->delete();
    }
}
