<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeEducationRequest;
use App\Models\Employee;
use App\Models\EmployeeEducation;
use Illuminate\Http\RedirectResponse;

class EmployeeEducationHistoryController extends Controller
{
    public function store(EmployeeEducationRequest $request, Employee $employee): RedirectResponse
    {
        abort_unless(auth()->user()->can('employee.update'), 403);

        $validated = $request->validated();

        $employee->educations()->create($validated);

        return back()->with('success', 'Riwayat pendidikan berhasil ditambahkan.');
    }

    public function update(EmployeeEducationRequest $request, Employee $employee, EmployeeEducation $education): RedirectResponse
    {
        $this->authorize('update', $education);

        $validated = $request->validated();
        $education->update($validated);

        return back()->with('success', 'Riwayat pendidikan berhasil diperbarui.');
    }

    public function destroy(Employee $employee, EmployeeEducation $education): RedirectResponse
    {
        $this->authorize('delete', $education);

        $education->delete();

        return back()->with('success', 'Riwayat pendidikan berhasil dihapus.');
    }
}
