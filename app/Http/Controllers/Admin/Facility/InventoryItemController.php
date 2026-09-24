<?php

namespace App\Http\Controllers\Admin\Facility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facility\AdjustInventoryQuantityRequest;
use App\Http\Requests\Facility\DisposeInventoryItemRequest;
use App\Http\Requests\Facility\StoreInventoryItemRequest;
use App\Http\Requests\Facility\UpdateInventoryItemRequest;
use App\Models\Branch;
use App\Models\InventoryItem;
use App\Repositories\Facility\InventoryItemRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryItemController extends Controller
{
    public function __construct(
        private readonly InventoryItemRepository $repository,
    ) {}

    public function index(): View
    {
        return view('facility.inventory.index');
    }

    public function create(Request $request): View
    {
        $employee = $request->user()->employee;

        $branches = match (true) {
            (bool) $employee?->branch_id => Branch::where('id', $employee->branch_id)->where('is_active', true)->get(),
            (bool) $employee?->area_id => Branch::where('areas_id', $employee->area_id)->where('is_active', true)->orderBy('branch_name')->get(),
            default => Branch::where('is_active', true)->orderBy('branch_name')->get(),
        };

        return view('facility.inventory.create', compact('branches'));
    }

    public function store(StoreInventoryItemRequest $request): RedirectResponse
    {
        $item = $this->repository->create($request->validated(), $request->user()->employee->id);

        return redirect()->route('facility.inventory.show', $item)->with('success', 'Inventaris berhasil ditambahkan.');
    }

    public function show(InventoryItem $inventoryItem): View
    {
        $inventoryItem->load(['branch', 'budgetEstimateItem', 'histories.changedBy']);

        return view('facility.inventory.show', compact('inventoryItem'));
    }

    public function edit(InventoryItem $inventoryItem): View
    {
        return view('facility.inventory.edit', compact('inventoryItem'));
    }

    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $this->repository->update($inventoryItem, $request->validated(), $request->user()->employee->id);

        return redirect()->route('facility.inventory.show', $inventoryItem)->with('success', 'Inventaris berhasil diperbarui.');
    }

    public function adjustQuantity(AdjustInventoryQuantityRequest $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $data = $request->validated();
        $this->repository->adjustQuantity($inventoryItem, $data['quantity'], $data['reason'], $request->user()->employee->id);

        return redirect()->route('facility.inventory.show', $inventoryItem)->with('success', 'Kuantitas berhasil diperbarui.');
    }

    public function dispose(DisposeInventoryItemRequest $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $this->repository->dispose($inventoryItem, $request->validated()['reason'], $request->user()->employee->id);

        return redirect()->route('facility.inventory.index')->with('success', 'Inventaris berhasil di-dispose.');
    }
}
