<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use App\Repositories\AreaRepository;
use App\Repositories\BranchRepository;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct(
        private readonly BranchRepository $repository,
        private readonly AreaRepository $areaRepository,
        private readonly EmployeeRepository $employeeRepository,
    ) {}

    public function index(): View
    {
        return view('organization.branch.index', [
            'branches' => $this->repository->all(),
        ]);
    }

    public function create(): View
    {
        return view('organization.branch.create', [
            'areas' => $this->areaRepository->all(),
        ]);
    }

    public function store(StoreBranchRequest $request): RedirectResponse
    {
        $this->repository->create($request->validated());

        return redirect()->route('branches.index')->with('success', 'Branch berhasil ditambahkan.');
    }

    public function edit(Branch $branch): View
    {
        return view('organization.branch.edit', [
            'branch' => $branch,
            'areas' => $this->areaRepository->all(),
        ]);
    }

    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $this->repository->update($branch, $request->validated());

        return redirect()->route('branches.index')->with('success', 'Branch berhasil diperbarui.');
    }

    public function toggleActive(Branch $branch): RedirectResponse
    {
        $this->authorize('organization.branch.toggle_active');

        $this->repository->toggleActive($branch);

        return redirect()->route('branches.index')->with('success', 'Status branch berhasil diubah.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $this->authorize('organization.branch.delete');

        $this->repository->delete($branch);

        return redirect()->route('branches.index')->with('success', 'Branch berhasil dihapus.');
    }
}
