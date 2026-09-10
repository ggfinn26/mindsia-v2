<?php

namespace App\Repositories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Collection;

class InstitutionRepository
{
    public function all(): Collection
    {
        return Institution::with('region')->orderBy('institution_name')->get();
    }

    public function find(int $id): Institution
    {
        return Institution::findOrFail($id);
    }

    public function byRegion(int $regionId): Collection
    {
        return Institution::where('regions_id', $regionId)->orderBy('institution_name')->get();
    }

    public function byJenjang(string $jenjang): Collection
    {
        return Institution::where('jenjang_institution', $jenjang)->orderBy('institution_name')->get();
    }

    public function create(array $data): Institution
    {
        return Institution::create($data);
    }

    public function update(Institution $institution, array $data): Institution
    {
        $institution->update($data);

        return $institution;
    }

    public function delete(Institution $institution): void
    {
        if ($institution->members()->exists()) {
            throw new \RuntimeException('Tidak bisa hapus institusi yang masih memiliki member.');
        }

        $institution->delete();
    }
}
