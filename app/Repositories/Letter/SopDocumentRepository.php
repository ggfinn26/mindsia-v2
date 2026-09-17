<?php

namespace App\Repositories\Letter;

use App\Models\SopDocument;
use Illuminate\Pagination\LengthAwarePaginator;

class SopDocumentRepository
{
    public function find(int $id): SopDocument
    {
        return SopDocument::with(['branch', 'uploadedBy'])->findOrFail($id);
    }

    public function visibleTo(string $roleName, ?int $branchId = null): LengthAwarePaginator
    {
        return SopDocument::where('is_active', true)
            ->where(fn ($q) => $q
                ->whereNull('branch_id')
                ->orWhere('branch_id', $branchId)
            )
            ->where(fn ($q) => $q
                ->whereNull('visible_to')
                ->orWhereJsonContains('visible_to', $roleName)
            )
            ->orderByDesc('created_at')
            ->paginate(15);
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
