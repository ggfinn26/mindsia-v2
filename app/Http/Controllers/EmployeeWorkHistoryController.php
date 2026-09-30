<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeWorkHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeWorkHistoryController extends Controller
{
    private function authorizeAction(Employee $employee): void
    {
        abort_unless(auth()->id() === $employee->user_id, 403);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'company_name' => ['required', 'string', 'max:200'],
            'position' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'reason_leaving' => ['nullable', 'string', 'max:500'],
        ]);
    }

    public function store(Request $request, Employee $employee): RedirectResponse
    {
        $this->authorizeAction($employee);
        $employee->workHistories()->create($this->validated($request));

        return back()->with('success', 'Pengalaman kerja berhasil ditambahkan.');
    }

    public function update(Request $request, Employee $employee, EmployeeWorkHistory $workHistory): RedirectResponse
    {
        abort_unless($workHistory->employee_id === $employee->id, 404);
        $this->authorizeAction($employee);
        $workHistory->update($this->validated($request));

        return back()->with('success', 'Pengalaman kerja berhasil diperbarui.');
    }

    public function destroy(Employee $employee, EmployeeWorkHistory $workHistory): RedirectResponse
    {
        abort_unless($workHistory->employee_id === $employee->id, 404);
        $this->authorizeAction($employee);
        $workHistory->delete();

        return back()->with('success', 'Pengalaman kerja berhasil dihapus.');
    }
}
