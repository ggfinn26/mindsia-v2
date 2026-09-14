<?php

namespace App\Observers;

use App\Models\BudgetEstimateItem;

class BudgetEstimateItemObserver
{
    public function saved(BudgetEstimateItem $item): void
    {
        $this->syncTotal($item);
    }

    public function deleted(BudgetEstimateItem $item): void
    {
        $this->syncTotal($item);
    }

    private function syncTotal(BudgetEstimateItem $item): void
    {
        $total = BudgetEstimateItem::where('budget_estimate_id', $item->budget_estimate_id)->sum('total_price');
        $item->budgetEstimate()->update(['total_amount' => $total]);
    }
}
