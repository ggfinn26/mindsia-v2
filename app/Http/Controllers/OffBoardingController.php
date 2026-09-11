<?php

namespace App\Http\Controllers;

use App\Http\Requests\OffBoardingRequest;
use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OffBoardingController extends Controller
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
    ) {}

    public function create(Employee $employee): View
    {
        $this->authorize('update', $employee);

        $current = $this->getCurrentStatus($employee);

        return view('employee.off-boarding.create', [
            'employee' => $employee,
            'current' => $current,
            'checklist' => $current->terminationChecklist,
        ]);
    }

    public function store(OffBoardingRequest $request, Employee $employee): RedirectResponse
    {
        $this->authorize('update', $employee);

        $current = $this->getCurrentStatus($employee);

        DB::transaction(function () use ($current, $employee, $request) {
            $current->offBoarding()->create($request->validated());
            $this->employeeRepository->deactivate($employee);
            if ($employee->user) {
                $employee->user->delete();
            }
        });

        return redirect()->route('employees.index')
            ->with('success', 'Employee berhasil di-offboard.');
    }

    private function getCurrentStatus(Employee $employee)
    {
        return $employee->currentStatus ?? abort(404, 'Employee tidak memiliki status kepegawaian aktif.');
    }
}
