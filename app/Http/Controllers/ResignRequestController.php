<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResignRequestFormRequest;
use App\Models\Employee;
use App\Models\ResignRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResignRequestController extends Controller
{
    public function create(Employee $employee): View
    {
        abort_unless(auth()->id() === $employee->user_id, 403);

        return view('admin.employees.resign-requests.create', compact('employee'));
    }

    public function store(ResignRequestFormRequest $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validated();
        $validated['employee_id'] = $employee->id;

        ResignRequest::create($validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Permintaan pengunduran diri berhasil diajukan.');
    }

    public function index(): View
    {
        $resignRequests = ResignRequest::with('employee')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.resign-requests.index', compact('resignRequests'));
    }

    public function show(ResignRequest $resignRequest): View
    {
        $this->authorize('view', $resignRequest);

        return view('admin.resign-requests.show', compact('resignRequest'));
    }

    public function approve(ResignRequest $resignRequest): RedirectResponse
    {
        $this->authorize('approve', $resignRequest);

        $resignRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('off-boarding.create', $resignRequest->employee_id)
            ->with('success', 'Permintaan pengunduran diri disetujui. Lanjutkan offboarding.');
    }

    public function reject(ResignRequest $resignRequest): RedirectResponse
    {
        $this->authorize('reject', $resignRequest);

        $resignRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Permintaan pengunduran diri ditolak.');
    }
}
