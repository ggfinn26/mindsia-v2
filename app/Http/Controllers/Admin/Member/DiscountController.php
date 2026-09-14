<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreDiscountRequest;
use App\Http\Requests\Member\SyncDiscountProgramsRequest;
use App\Http\Requests\Member\UpdateDiscountRequest;
use App\Models\Discount;
use App\Repositories\Member\DiscountRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function __construct(
        private readonly DiscountRepository $repo,
    ) {}

    public function index(): View
    {
        return view('admin.member.discounts.index', [
            'discounts' => $this->repo->all(),
        ]);
    }

    public function store(StoreDiscountRequest $request): RedirectResponse
    {
        $discount = $this->repo->store($request->validated());

        return redirect()->route('discounts.show', $discount)->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function show(Discount $discount): View
    {
        $discount->load(['programs.program', 'registrations']);

        return view('admin.member.discounts.show', compact('discount'));
    }

    public function update(UpdateDiscountRequest $request, Discount $discount): RedirectResponse
    {
        $this->repo->update($discount, $request->validated());

        return redirect()->route('discounts.show', $discount)->with('success', 'Diskon berhasil diperbarui.');
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        abort_unless(auth()->user()->can('member.manage'), 403);

        $this->repo->delete($discount);

        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil dihapus.');
    }

    public function toggleActive(Discount $discount): RedirectResponse
    {
        abort_unless(auth()->user()->can('member.manage'), 403);

        $discount->update(['is_active' => ! $discount->is_active]);

        return back()->with('success', 'Status diskon berhasil diperbarui.');
    }

    public function syncPrograms(SyncDiscountProgramsRequest $request, Discount $discount): RedirectResponse
    {
        $this->repo->assignPrograms($discount, $request->validated('program_ids', []));

        return redirect()->route('discounts.show', $discount)->with('success', 'Eligibilitas program diskon berhasil diperbarui.');
    }
}
