<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreReimbursementItemRequest;
use App\Models\Reimbursement;
use App\Models\ReimbursementItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReimbursementItemController extends Controller
{
    public function store(StoreReimbursementItemRequest $request, Reimbursement $reimbursement): RedirectResponse
    {
        abort_unless($reimbursement->isDraft(), 403, 'Hanya draft yang bisa diedit.');
        abort_unless($reimbursement->employee_id === $request->user()->employee->id, 403);

        $reimbursement->items()->create($request->validated());

        return back()->with('success', 'Item berhasil ditambahkan.');
    }

    public function destroy(Request $request, Reimbursement $reimbursement, ReimbursementItem $item): RedirectResponse
    {
        abort_unless($reimbursement->isDraft(), 403, 'Hanya draft yang bisa diedit.');
        abort_unless($reimbursement->employee_id === $request->user()->employee->id, 403);
        abort_unless($item->reimbursement_id === $reimbursement->id, 404);

        $item->delete();

        return back()->with('success', 'Item dihapus.');
    }
}
