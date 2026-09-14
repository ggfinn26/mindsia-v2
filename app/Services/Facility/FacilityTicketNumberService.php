<?php

namespace App\Services\Facility;

use App\Models\Branch;
use App\Models\FacilityTicket;
use Carbon\Carbon;

class FacilityTicketNumberService
{
    public function generate(string $category, int $branchId): string
    {
        $branch = Branch::findOrFail($branchId);
        $branchCode = strtoupper($branch->code_branches);
        $dateStr = Carbon::today()->format('Ymd');
        $catSlug = $this->slugifyCategory($category);

        $sequence = FacilityTicket::where('branch_id', $branchId)
            ->where('category', $category)
            ->whereDate('created_at', today())
            ->count() + 1;

        return sprintf('TIKET-%s-%s-%s-%03d', $catSlug, $branchCode, $dateStr, $sequence);
    }

    private function slugifyCategory(string $category): string
    {
        return substr(strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $category)), 0, 10);
    }
}
