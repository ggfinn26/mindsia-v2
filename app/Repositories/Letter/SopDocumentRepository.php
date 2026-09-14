<?php

namespace App\Repositories\Letter;

use App\Models\SopDocument;
use Illuminate\Database\Eloquent\Collection;

class SopDocumentRepository
{
    public function find(int $id): SopDocument
    {
        return SopDocument::with(['branch', 'uploadedBy'])->findOrFail($id);
    }

    public function visibleTo(string $roleName, ?int $branchId = null): Collection
    {
        return SopDocument::where('is_active', true)
            ->where(fn ($q) => $q
                ->whereNull('branch_id')
                ->orWhere('branch_id', $branchId)
            )
            ->get()
            ->filter(fn ($sop) => $sop->isVisibleToRole($roleName))
            ->values();
    }

    public function create(array $data): SopDocument
    {
        return SopDocument::create($data);
    }

    public function update(SopDocument $sop, array $data): SopDocument
    {
        $sop->update($data);

        return $sop;
    }

    public function deactivate(SopDocument $sop): void
    {
        $sop->update(['is_active' => false]);
    }
}
