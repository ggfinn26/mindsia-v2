<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignBranchPICRequest;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Area;
use App\Models\Branch;
use App\Models\Employee;
use App\Repositories\AreaRepository;
use App\Repositories\BranchRepository;
use App\Repositories\EmployeeRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct(
        private readonly BranchRepository $repository,
        private readonly AreaRepository $areaRepository,
        private readonly EmployeeRepository $employeeRepository,
    ) {}

    public function index(Request $request): View
    {
        $query = Branch::with(['area.region', 'picEmployee']);

        if ($request->filled('area_id')) {
            $query->where('areas_id', $request->area_id);
        }

        if ($request->filled('pic_id')) {
            if ($request->pic_id === 'unassigned') {
                $query->whereNull('ma_pic_employee_id');
            } else {
                $query->where('ma_pic_employee_id', $request->pic_id);
            }
        }

        $sort = $request->query('sort', 'branch_name');
        $direction = $request->query('direction', 'asc');

        if ($sort === 'area') {
            $query->join('areas', 'branches.areas_id', '=', 'areas.id')
                ->orderBy('areas.name', $direction)
                ->select('branches.*');
        } elseif ($sort === 'pic') {
            $query->leftJoin('employees', 'branches.ma_pic_employee_id', '=', 'employees.id')
                ->orderBy('employees.full_name', $direction)
                ->select('branches.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        return view('organization.branch.index', [
            'branches' => $query->get(),
            'allAreas' => Area::orderBy('name')->get(),
            'allPics' => Employee::orderBy('full_name')->get(),
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

    public function assignPic(AssignBranchPICRequest $request, Branch $branch): RedirectResponse
    {
        $this->repository->assignPic($branch, $request->input('ma_pic_employee_id'));

        return redirect()->route('branches.index')->with('success', 'PIC branch berhasil diassign.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $this->authorize('organization.branch.delete');

        try {
            $this->repository->delete($branch);

            return redirect()->route('branches.index')->with('success', 'Branch berhasil dihapus.');
        } catch (\RuntimeException|QueryException $e) {
            return redirect()->route('branches.index')->with('error', $e->getMessage());
        }
    }
}
