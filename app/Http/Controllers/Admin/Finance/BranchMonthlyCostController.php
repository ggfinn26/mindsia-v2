<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreBranchMonthlyCostRequest;
use App\Http\Requests\Finance\UpdateBranchMonthlyCostRequest;
use App\Models\Branch;
use App\Models\BranchMonthlyCost;
use App\Repositories\Finance\BranchMonthlyCostRepository;
use App\Services\Finance\BranchPeriodLockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchMonthlyCostController extends Controller
{
    public function __construct(
        private readonly BranchMonthlyCostRepository $repository,
        private readonly BranchPeriodLockService $lockService,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('finance.monthly_cost.view'), 403);

        $employee = $request->user()->employee;

        $branches = match (true) {
            (bool) $employee?->branch_id => Branch::where('id', $employee->branch_id)->where('is_active', true)->get(),
            (bool) $employee?->area_id => Branch::where('areas_id', $employee->area_id)->where('is_active', true)->orderBy('branch_name')->get(),
            default => Branch::where('is_active', true)->orderBy('branch_name')->get(),
        };

        $branchId = (int) $request->input('branch_id', $employee?->branch_id);
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        if ($branchId) {
            $costs = $this->repository->list($branchId, $year, $month);
            $isLocked = $this->lockService->isLocked($branchId, $year, $month);
        } else {
            $costs = collect();
            $isLocked = false;
        }

        return view('finance.monthly-cost.index', compact('costs', 'isLocked', 'year', 'month', 'branchId', 'branches'));
    }

    public function store(StoreBranchMonthlyCostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->lockService->assertUnlocked((int) $data['branch_id'], (int) $data['period_year'], (int) $data['period_month']);

        $this->repository->create(array_merge($data, [
            'recorded_by_employee_id' => $request->user()->employee->id,
        ]));

        return back()->with('success', 'Biaya berhasil dicatat.');
    }

    public function update(UpdateBranchMonthlyCostRequest $request, BranchMonthlyCost $branchMonthlyCost): RedirectResponse
    {
        $this->lockService->assertUnlocked(
            $branchMonthlyCost->branch_id,
            $branchMonthlyCost->period_year,
            $branchMonthlyCost->period_month
        );

        $this->repository->update($branchMonthlyCost, $request->validated());

        return back()->with('success', 'Biaya berhasil diperbarui.');
    }

    public function destroy(Request $request, BranchMonthlyCost $branchMonthlyCost): RedirectResponse
    {
        abort_unless($request->user()->can('finance.monthly_cost.delete'), 403);
        $this->lockService->assertUnlocked(
            $branchMonthlyCost->branch_id,
            $branchMonthlyCost->period_year,
            $branchMonthlyCost->period_month
        );

        $this->repository->delete($branchMonthlyCost);

        return back()->with('success', 'Biaya berhasil dihapus.');
    }
}
