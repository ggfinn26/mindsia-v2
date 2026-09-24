<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSocialization;
use App\Repositories\Marketing\ProspectiveMemberRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MySocializationController extends Controller
{
    public function __construct(
        private readonly ProspectiveMemberRepository $pmRepository,
    ) {}

    public function index(Request $request): View
    {
        $employee = $request->user()->employee;

        abort_unless($employee, 403);

        $socializations = EmployeeSocialization::with(['socialization.institution', 'socialization.schedules'])
            ->where('employee_id', $employee->id)
            ->latest()
            ->paginate(20);

        return view('marketing.my-socialization.index', compact('socializations'));
    }

    public function show(Request $request, EmployeeSocialization $mySocialization): View
    {
        $employee = $request->user()->employee;

        abort_unless($employee && $mySocialization->employee_id === $employee->id, 403);

        $mySocialization->load(['socialization.institution', 'socialization.schedules', 'socialization.createdBy']);

        $leads = $this->pmRepository->paginate([
            'socialization_id' => $mySocialization->socialization_id,
            'employee_id' => $employee->id,
            'status' => $request->input('pm_status'),
            'search' => $request->input('pm_search'),
            'sort' => $request->input('pm_sort', 'full_name'),
            'dir' => $request->input('pm_dir', 'asc'),
        ], 10);

        return view('marketing.my-socialization.show', compact('mySocialization', 'leads'));
    }

    public function update(Request $request, EmployeeSocialization $mySocialization): RedirectResponse
    {
        $employee = $request->user()->employee;

        abort_unless($employee && $mySocialization->employee_id === $employee->id, 403);
        abort_if($mySocialization->status === 'confirmed', 403, 'Data sudah dikonfirmasi, tidak bisa diubah.');

        $validated = $request->validate([
            'classes_obtained' => ['required', 'integer', 'min:0'],
        ]);

        $mySocialization->update($validated);

        return back()->with('success', 'Jumlah kelas berhasil disimpan.');
    }
}
