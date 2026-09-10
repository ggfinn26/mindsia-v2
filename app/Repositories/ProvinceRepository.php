<?php

namespace App\Repositories;

use App\Models\Province;
use Illuminate\Database\Eloquent\Collection;

class ProvinceRepository
{
    public function all(): Collection
    {
        return Province::orderBy('name')->get();
    }

    public function find(int $id): Province
    {
        return Province::findOrFail($id);
    }

    public function create(array $data): Province
    {
        return Province::create($data);
    }

    public function update(Province $province, array $data): Province
    {
        $province->update($data);

        return $province;
    }

    public function delete(Province $province): void
    {
        if ($province->regions()->exists()) {
            throw new \RuntimeException('Tidak bisa hapus provinsi yang masih memiliki region.');
        }

        $province->delete();
    }
}
