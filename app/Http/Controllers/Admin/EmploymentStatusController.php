<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentStatusRequest;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Repositories\PositionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmploymentStatusController extends Controller
{
    public function __construct(
        private readonly PositionRepository $positionRepository,
    ) {
        $this->middleware('board-of-directors');
    }

    public function create(Employee $employee): View
    {
        $positions = $this->positionRepository->all();

        return view('admin.employees.employment-status.create', compact('employee', 'positions'));
    }

    public function store(EmploymentStatusRequest $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validated();
        $tab = $validated['tab'];

        if ($tab === 'cepat') {
            $this->storeCepat($request, $employee, $validated);
        } else {
            $this->storeLengkap($employee, $validated);
        }

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Kontrak karyawan berhasil dibuat.');
    }

    private function storeLengkap(Employee $employee, array $validated): void
    {
        $employee->employmentStatuses()->create([
            'type_employment' => $validated['type_employment'],
            'join_date' => $validated['join_date'],
            'position_id' => $validated['position_id'],
            'contract_start_date' => $validated['contract_start_date'],
            'contract_end_date' => $validated['contract_end_date'] ?? null,
            'setup_incomplete' => false,
        ]);
    }

    private function storeCepat(EmploymentStatusRequest $request, Employee $employee, array $validated): void
    {
        $path = null;
        if ($request->hasFile('contract_file_path')) {
            $file = $request->file('contract_file_path');
            $path = $file->store('contracts', 'public');
        }

        $employee->employmentStatuses()->create([
            'type_employment' => $validated['type_employment'],
            'join_date' => $validated['join_date'],
            'position_id' => $validated['position_id'],
            'contract_file_path' => $path,
            'setup_incomplete' => true,
        ]);
    }

    public function edit(EmploymentStatus $status): View
    {
        $positions = $this->positionRepository->all();

        return view('admin.employees.employment-status.edit', compact('status', 'positions'));
    }

    public function update(EmploymentStatusRequest $request, EmploymentStatus $status): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'type_employment' => $validated['type_employment'],
            'join_date' => $validated['join_date'],
            'position_id' => $validated['position_id'],
        ];

        if (array_key_exists('contract_start_date', $validated)) {
            $data['contract_start_date'] = $validated['contract_start_date'];
            $data['contract_end_date'] = $validated['contract_end_date'];
        }

        $status->update($data);

        return back()->with('success', 'Kontrak karyawan berhasil diperbarui.');
    }
}
