<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreBudgetEstimateItemRequest;
use App\Models\BudgetEstimate;
use App\Models\BudgetEstimateItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BudgetEstimateItemController extends Controller
{
    public function store(StoreBudgetEstimateItemRequest $request, BudgetEstimate $budgetEstimate): RedirectResponse
    {
        abort_unless($budgetEstimate->isDraft(), 403, 'Hanya draft yang bisa diedit.');

        $data = $request->validated();

        $budgetEstimate->items()->create([
            'item_name' => $data['item_name'],
            'quantity' => $data['quantity'],
            'estimated_price' => $data['estimated_price'],
            'total_price' => $data['quantity'] * $data['estimated_price'],
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Item berhasil ditambahkan.');
    }

    public function destroy(Request $request, BudgetEstimate $budgetEstimate, BudgetEstimateItem $item): RedirectResponse
    {
        abort_unless($request->user()->can('finance.budget_estimate.create'), 403);
        abort_unless($budgetEstimate->isDraft(), 403, 'Hanya draft yang bisa diedit.');
        abort_unless($item->budget_estimate_id === $budgetEstimate->id, 404);

        $item->delete();

        return back()->with('success', 'Item dihapus.');
    }
}
