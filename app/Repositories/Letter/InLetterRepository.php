<?php

namespace App\Repositories\Letter;

use App\Models\InLetter;
use Illuminate\Database\Eloquent\Collection;

class InLetterRepository
{
    public function find(int $id): InLetter
    {
        return InLetter::with(['branch', 'pic', 'uploadedBy'])->findOrFail($id);
    }

    public function byBranch(int $branchId): Collection
    {
        return InLetter::where('branch_id', $branchId)
            ->orderByDesc('receive_date')
            ->get();
    }

    public function create(array $data): InLetter
    {
        return InLetter::create($data);
    }

    public function update(InLetter $letter, array $data): InLetter
    {
        $letter->update($data);

        return $letter;
    }

    public function delete(InLetter $letter): void
    {
        $letter->delete();
    }
}
