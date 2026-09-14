<?php

namespace App\Observers;

use App\Models\ReimbursementItem;

class ReimbursementItemObserver
{
    public function saved(ReimbursementItem $item): void
    {
        $this->syncTotal($item);
    }

    public function deleted(ReimbursementItem $item): void
    {
        $this->syncTotal($item);
    }

    private function syncTotal(ReimbursementItem $item): void
    {
        $total = ReimbursementItem::where('reimbursement_id', $item->reimbursement_id)->sum('amount');
        $item->reimbursement()->update(['total_amount' => $total]);
    }
}
