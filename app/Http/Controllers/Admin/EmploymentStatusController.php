<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmploymentStatusController extends Controller
{
    public function create(Employee $employee): View
    {
        return view('admin.employees.employment-status.create', compact('employee'));
    }

    public function store(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'type_employment' => ['required', 'string'],
            'join_date' => ['required', 'date'],
            'contract_start_date' => ['required', 'date'],
            'contract_end_date' => ['nullable', 'date'],
            'position_id' => ['required', 'exists:positions,id'],
        ]);

        $employee->employmentStatuses()->create($validated);
        return redirect()->route('employees.show', $employee)->with('success', 'Status ketenagakerjaan ditambahkan.');
    }

    public function edit(EmploymentStatus $status): View
    {
        return view('admin.employees.employment-status.edit', compact('status'));
    }

    public function update(Request $request, EmploymentStatus $status): RedirectResponse
    {
        $validated = $request->validate([
            'type_employment' => ['required', 'string'],
            'join_date' => ['required', 'date'],
            'contract_start_date' => ['required', 'date'],
            'contract_end_date' => ['nullable', 'date'],
            'position_id' => ['required', 'exists:positions,id'],
        ]);

        $status->update($validated);
        return back()->with('success', 'Status ketenagakerjaan diperbarui.');
    }
}
