<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\StoreEmployeeCompensationRequest;
use App\Http\Requests\Payroll\UpdateEmployeeCompensationRequest;
use App\Models\Employee;
use App\Models\EmployeeCompensation;
use App\Repositories\Payroll\PayrollComponentRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeCompensationController extends Controller
{
    public function __construct(
        private readonly PayrollComponentRepository $componentRepo,
    ) {}

    public function index(Employee $employee): View
    {
        return view('admin.payroll.compensations.index', [
            'employee' => $employee,
            'components' => $this->componentRepo->allActive(),
            'compensations' => $employee->compensations()->with('payrollComponent')->get(),
        ]);
    }

    public function store(StoreEmployeeCompensationRequest $request, Employee $employee): RedirectResponse
    {
        EmployeeCompensation::updateOrCreate(
            ['employee_id' => $employee->id, 'payroll_component_id' => $request->input('payroll_component_id')],
            ['value' => $request->input('value'), 'notes' => $request->input('notes')],
        );

        return redirect()->route('payroll.compensations.index', $employee)->with('success', 'Kompensasi berhasil disimpan.');
    }

    public function update(UpdateEmployeeCompensationRequest $request, Employee $employee, EmployeeCompensation $compensation): RedirectResponse
    {
        $compensation->update($request->validated());

        return redirect()->route('payroll.compensations.index', $employee)->with('success', 'Kompensasi berhasil diperbarui.');
    }

    public function destroy(Employee $employee, EmployeeCompensation $compensation): RedirectResponse
    {
        abort_if($compensation->employee_id !== $employee->id, 403);

        $compensation->delete();

        return redirect()->route('payroll.compensations.index', $employee)->with('success', 'Kompensasi berhasil dihapus.');
    }
}
