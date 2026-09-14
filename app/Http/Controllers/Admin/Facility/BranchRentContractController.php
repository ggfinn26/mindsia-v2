<?php

namespace App\Http\Controllers\Admin\Facility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facility\StoreBranchRentContractRequest;
use App\Http\Requests\Facility\UpdateBranchRentContractRequest;
use App\Models\BranchRentContract;
use App\Repositories\Facility\BranchRentContractRepository;
use App\Services\Facility\BranchRentContractService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BranchRentContractController extends Controller
{
    public function __construct(
        private readonly BranchRentContractRepository $repository,
        private readonly BranchRentContractService $service,
    ) {}

    public function index(): View
    {
        return view('facility.rent-contract.index');
    }

    public function create(): View
    {
        return view('facility.rent-contract.create');
    }

    public function store(StoreBranchRentContractRequest $request): RedirectResponse
    {
        $contract = $this->service->create($request->validated(), $request->user()->employee->id);

        return redirect()->route('facility.rent-contracts.show', $contract)->with('success', 'Kontrak sewa berhasil dibuat, termin di-generate otomatis.');
    }

    public function show(BranchRentContract $branchRentContract): View
    {
        $branchRentContract->load(['branch', 'area', 'createdBy', 'termins.paidBy']);

        return view('facility.rent-contract.show', compact('branchRentContract'));
    }

    public function edit(BranchRentContract $branchRentContract): View
    {
        return view('facility.rent-contract.edit', compact('branchRentContract'));
    }

    public function update(UpdateBranchRentContractRequest $request, BranchRentContract $branchRentContract): RedirectResponse
    {
        $this->repository->update($branchRentContract, $request->validated());

        return redirect()->route('facility.rent-contracts.show', $branchRentContract)->with('success', 'Kontrak berhasil diperbarui.');
    }

    public function destroy(BranchRentContract $branchRentContract): RedirectResponse
    {
        $this->repository->delete($branchRentContract);

        return redirect()->route('facility.rent-contracts.index')->with('success', 'Kontrak berhasil dihapus.');
    }
}
