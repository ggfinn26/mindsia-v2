<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchProgramQuotaRequest;
use App\Http\Requests\UpdateBranchProgramQuotaRequest;
use App\Models\Branch;
use App\Models\BranchProgramQuota;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class BranchProgramQuotaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:curriculum.quota.create'),
        ];
    }

    public function index(Program $program): View
    {
        $quotas = $program->branchQuotas()->get()->keyBy('branch_id');
        $branches = Branch::active()->orderBy('branch_name')->get();

        return view('program.quota.index', compact('program', 'quotas', 'branches'));
    }

    public function create(Program $program): RedirectResponse
    {
        // Quota creation is inline on the index page
        return redirect()->route('programs.quotas.index', $program);
    }

    public function store(StoreBranchProgramQuotaRequest $request, Program $program): RedirectResponse
    {
        $quotas = $request->input('quotas', []);

        foreach ($quotas as $branchId => $limit) {
            if ($limit !== null && $limit !== '') {
                BranchProgramQuota::updateOrCreate(
                    ['branch_id' => $branchId, 'program_id' => $program->id],
                    ['quota_limit' => $limit]
                );
            } else {
                BranchProgramQuota::where('branch_id', $branchId)
                    ->where('program_id', $program->id)
                    ->delete();
            }
        }

        return back()->with('success', 'Kuota berhasil disimpan.');
    }

    public function show(BranchProgramQuota $quota): RedirectResponse
    {
        return redirect()->route('programs.quotas.index', $quota->program_id);
    }

    public function edit(BranchProgramQuota $quota): RedirectResponse
    {
        return redirect()->route('programs.quotas.index', $quota->program_id);
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
