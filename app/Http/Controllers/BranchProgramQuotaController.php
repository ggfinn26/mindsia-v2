<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchProgramQuotaRequest;
use App\Http\Requests\UpdateBranchProgramQuotaRequest;
use App\Models\BranchProgramQuota;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BranchProgramQuotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('board-of-directors');
    }

    public function index(Program $program): View
    {
        $quotas = $program->branchQuotas()->with('branch')->get();
        $branches = \App\Models\Branch::active()->orderBy('branch_name')->get();

        return view('program.quota.index', compact('program', 'quotas', 'branches'));
    }

    public function store(StoreBranchProgramQuotaRequest $request, Program $program): RedirectResponse
    {
        BranchProgramQuota::updateOrCreate(
            ['branch_id' => $request->input('branch_id'), 'program_id' => $program->id],
            ['quota_limit' => $request->input('quota_limit')]
        );

        return back()->with('success', 'Kuota berhasil disimpan.');
    }

    public function update(UpdateBranchProgramQuotaRequest $request, Program $program, BranchProgramQuota $quota): RedirectResponse
    {
        $quota->update($request->validated());

        return back()->with('success', 'Kuota berhasil diperbarui.');
    }

    public function destroy(Program $program, BranchProgramQuota $quota): RedirectResponse
    {
        $quota->delete();

        return back()->with('success', 'Kuota berhasil dihapus.');
    }
}
