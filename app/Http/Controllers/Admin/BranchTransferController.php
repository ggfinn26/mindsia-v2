<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchTransferRequest;
use App\Http\Requests\ReviewBranchTransferRequest;
use App\Models\BranchTransferRequest as BranchTransferModel;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BranchTransferController extends Controller
{
    public function requestCreate(Employee $employee): View
    {
        $this->authorize('requestBranchTransfer', $employee);

        $currentBranch = $employee->branch;
        $otherBranches = $employee->region
            ->branches()
            ->where('id', '!=', $employee->branch_id)
            ->active()
            ->get();

        return view('admin.employees.branch-transfer.request', compact(
            'employee',
            'currentBranch',
            'otherBranches'
        ));
    }

    public function requestStore(BranchTransferRequest $request, Employee $employee): RedirectResponse
    {
        $this->authorize('requestBranchTransfer', $employee);

        $validated = $request->validated();

        BranchTransferModel::create([
            'employee_id' => $employee->id,
            'from_branch_id' => $employee->branch_id,
            'to_branch_id' => $validated['to_branch_id'],
            'status' => 'review',
        ]);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Permintaan pindah cabang telah dikirim ke HR.');
    }

    public function reviewIndex(): View
    {
        $this->authorize('organization.branch_transfer.review');

        $transfers = BranchTransferModel::with(['employee', 'fromBranch', 'toBranch'])
            ->where('status', 'review')
            ->latest()
            ->paginate(15);

        return view('admin.employees.branch-transfer.review', compact('transfers'));
    }

    public function reviewShow(BranchTransferModel $transfer): View
    {
        $this->authorize('organization.branch_transfer.review');

        $transfer->load(['employee', 'fromBranch', 'toBranch']);

        return view('admin.employees.branch-transfer.show', compact('transfer'));
    }

    public function reviewUpdate(ReviewBranchTransferRequest $request, BranchTransferModel $transfer): RedirectResponse
    {
        $this->authorize('organization.branch_transfer.review');

        $validated = $request->validated();

        $transfer->update([
            'status' => $validated['status'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($transfer->isApproved()) {
            $transfer->employee->update(['branch_id' => $transfer->to_branch_id]);

            return redirect()
                ->route('employees.show', $transfer->employee)
                ->with('success', 'Permintaan pindah cabang telah disetujui. Cabang karyawan berhasil diperbarui.');
        }

        return redirect()
            ->route('employees.show', $transfer->employee)
            ->with('success', 'Permintaan pindah cabang telah ditolak.');
    }

    public function directEdit(Employee $employee): View
    {
        $this->authorize('directBranchTransfer', $employee);

        $currentBranch = $employee->branch;
        $otherBranches = $employee->region
            ->branches()
            ->where('id', '!=', $employee->branch_id)
            ->active()
            ->get();

        return view('admin.employees.branch-transfer.direct-edit', compact(
            'employee',
            'currentBranch',
            'otherBranches'
        ));
    }

    public function directUpdate(BranchTransferRequest $request, Employee $employee): RedirectResponse
    {
        $this->authorize('directBranchTransfer', $employee);

        $validated = $request->validated();

        $employee->update(['branch_id' => $validated['to_branch_id']]);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Cabang karyawan berhasil diperbarui.');
    }
}
