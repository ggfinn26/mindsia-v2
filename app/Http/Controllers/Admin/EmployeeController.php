<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Repositories\AreaRepository;
use App\Repositories\BranchRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\RegionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeRepository $repository,
        private readonly RegionRepository $regionRepository,
        private readonly AreaRepository $areaRepository,
        private readonly BranchRepository $branchRepository,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('view', Employee::class);

        $employees = $this->repository->paginate(
            $request->only('region_id', 'branch_id', 'is_active', 'search'),
            auth()->user()
        );
        $regions = $this->regionRepository->all();

        return view('admin.employees.index', compact('employees', 'regions'));
    }

    public function create(): View
    {
        $this->authorize('create', Employee::class);

        $regions = $this->regionRepository->all();
        $areas = $this->areaRepository->all();
        $branches = $this->branchRepository->active();

        return view('admin.employees.create', compact('regions', 'areas', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Employee::class);

        $validated = $request->validate([
            'employee_code' => ['required', 'string', 'unique:employees'],
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:M,F'],
            'birthdate' => ['required', 'date'],
            'email' => ['required', 'email', 'unique:employees'],
            'whatsapp_number' => ['required', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        $this->repository->create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee berhasil ditambahkan.');
    }

    public function show(Employee $employee): View
    {
        $this->authorize('view', $employee);

        $employee = $this->repository->find($employee->id, auth()->user());

        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $this->authorize('update', $employee);

        $employee = $this->repository->find($employee->id, auth()->user());
        $regions = $this->regionRepository->all();
        $areas = $this->areaRepository->all();
        $branches = $this->branchRepository->active();

        return view('admin.employees.edit', compact('employee', 'regions', 'areas', 'branches'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $this->authorize('update', $employee);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:M,F'],
            'birthdate' => ['required', 'date'],
            'email' => ['required', 'email', 'unique:employees,email,'.$employee->id],
            'whatsapp_number' => ['required', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        $this->repository->update($employee, $validated);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee berhasil diperbarui.');
    }
}
