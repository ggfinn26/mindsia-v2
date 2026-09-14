<?php

namespace App\Repositories\Letter;

use App\Models\OutLetterViaUpload;
use Illuminate\Database\Eloquent\Collection;

class OutLetterViaUploadRepository
{
    public function find(int $id): OutLetterViaUpload
    {
        return OutLetterViaUpload::with(['branch', 'uploadedBy'])->findOrFail($id);
    }

    public function byBranch(int $branchId): Collection
    {
        return OutLetterViaUpload::where('branch_id', $branchId)
            ->orderByDesc('letter_date')
            ->get();
    }

    public function create(array $data): OutLetterViaUpload
    {
        return OutLetterViaUpload::create($data);
    }

    public function update(OutLetterViaUpload $letter, array $data): OutLetterViaUpload
    {
        $letter->update($data);

        return $letter;
    }

    public function delete(OutLetterViaUpload $letter): void
    {
        $letter->delete();
    }
}
