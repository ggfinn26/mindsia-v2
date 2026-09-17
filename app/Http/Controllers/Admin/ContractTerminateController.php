<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmploymentStatus;
use App\Models\TerminationChecklist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractTerminateController extends Controller
{
    public function initiate(EmploymentStatus $status): View
    {
        $this->authorize('terminateContract', $status->employee);

        return view('admin.employees.contract-terminate.initiate', compact('status'));
    }

    public function initiateStore(Request $request, EmploymentStatus $status): RedirectResponse
    {
        $this->authorize('terminateContract', $status->employee);

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'checklist_items' => ['required', 'array', 'min:1'],
            'checklist_items.*' => ['required', 'string', 'max:255'],
        ]);

        $status->update(['status' => 'terminated']);

        $items = collect($request->input('checklist_items'))
            ->map(fn ($item) => [
                'employment_status_id' => $status->id,
                'item_name' => $item,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        TerminationChecklist::insert($items);

        return redirect()
            ->route('employees.show', $status->employee)
            ->with('success', 'Proses terminasi kontrak telah dimulai. Checklist siap diisi.');
    }

    public function checklistShow(EmploymentStatus $status): View
    {
        $this->authorize('employee.termination_checklist.review');

        $checklist = $status->terminationChecklist;
        $completedCount = $checklist->where('is_completed', true)->count();

        return view('admin.employees.contract-terminate.checklist', compact('status', 'checklist', 'completedCount'));
    }

    public function checklistUpdate(Request $request, EmploymentStatus $status): RedirectResponse
    {
        $this->authorize('employee.termination_checklist.review');

        $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:termination_checklist,id'],
            'items.*.is_completed' => ['required', 'boolean'],
        ]);

        $itemIds = array_column($request->input('items'), 'id');
        $checklists = $status->terminationChecklist()->whereIn('id', $itemIds)->get()->keyBy('id');

        abort_if($checklists->count() !== count($itemIds), 403);

        foreach ($request->input('items') as $item) {
            $checklist = $checklists[$item['id']];

            if ($item['is_completed'] && ! $checklist->is_completed) {
                $checklist->complete(auth()->user());
            } elseif (! $item['is_completed'] && $checklist->is_completed) {
                $checklist->uncomplete();
            }
        }

        $allCompleted = $status->terminationChecklist()->where('is_completed', false)->count() === 0;

        if ($allCompleted) {
            return redirect()
                ->route('contract-terminate.complete.confirm', $status)
                ->with('success', 'Semua item checklist telah diselesaikan. Siap untuk hard delete.');
        }

        return back()->with('success', 'Checklist telah diperbarui.');
    }

    public function completeConfirmation(EmploymentStatus $status): View
    {
        $this->authorize('employee.termination_checklist.review');

        return view('admin.employees.contract-terminate.complete-confirmation', compact('status'));
    }

    public function complete(Request $request, EmploymentStatus $status): RedirectResponse
    {
        $this->authorize('employee.termination_checklist.review');
        $this->authorize('terminateContract', $status->employee);

        $request->validate([
            'confirm' => ['required', 'accepted'],
        ]);

        abort_if($status->terminationChecklist()->where('is_completed', false)->exists(), 403);
        abort_if($status->status !== 'terminated', 403);

        $user = $status->employee->user;
        if ($user) {
            $user->forceDelete();
        }

        $status->employee->update(['is_active' => false]);

        return redirect()
            ->route('employees.show', $status->employee)
            ->with('success', 'Kontrak karyawan telah diterminasi. Akun user telah dihapus.');
    }
}
