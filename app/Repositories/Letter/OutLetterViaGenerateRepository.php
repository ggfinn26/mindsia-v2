<?php

namespace App\Repositories\Letter;

use App\Models\OutLetterViaGenerate;
use Illuminate\Database\Eloquent\Collection;

class OutLetterViaGenerateRepository
{
    public function find(int $id): OutLetterViaGenerate
    {
        return OutLetterViaGenerate::with(['template', 'branch', 'signer', 'createdBy', 'publishedBy'])
            ->findOrFail($id);
    }

    public function byBranch(int $branchId, ?string $status = null): Collection
    {
        return OutLetterViaGenerate::where('branch_id', $branchId)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('letter_date')
            ->get();
    }

    public function update(OutLetterViaGenerate $letter, array $data): OutLetterViaGenerate
    {
        $letter->update($data);

        return $letter;
    }
}
