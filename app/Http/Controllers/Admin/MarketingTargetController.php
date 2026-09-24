<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\StoreMarketingTargetDefaultRequest;
use App\Http\Requests\Marketing\StoreMarketingTargetRequest;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\MarketingTarget;
use App\Models\MarketingTargetDefault;
use App\Models\Position;
use App\Repositories\Marketing\MarketingTargetRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingTargetController extends Controller
{
    public function __construct(
        private readonly MarketingTargetRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('marketing.target.set'), 403);

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $positions = Position::orderBy('hierarchy_order')->get();

        $positionTargets = MarketingTargetDefault::where('period_month', $month)
            ->where('period_year', $year)
            ->get()
            ->keyBy('position_id');

        $search = $request->input('search');
        $branchId = $request->input('branch_id');

        $branches = Branch::active()->orderBy('branch_name')->get(['id', 'branch_name']);

        $employees = Employee::where('is_active', true)
            ->with('currentStatus.position')
            ->when($search, function ($query, $search) {
                $query->where('full_name', 'like', "%{$search}%");
            })
            ->when($branchId, function ($query, $branchId) {
                $query->where('branch_id', $branchId);
            })
            ->orderBy('full_name')
            ->paginate(20)
            ->withQueryString();

        $employeeTargets = MarketingTarget::where('period_month', $month)
            ->where('period_year', $year)
            ->get()
            ->keyBy('employee_id');

        return view('marketing.target.index', compact(
            'month', 'year', 'positions', 'positionTargets', 'employees', 'employeeTargets', 'branches', 'search', 'branchId'
        ));
    }

    public function setEmployeeTarget(StoreMarketingTargetRequest $request): RedirectResponse
    {
        $this->repository->setEmployeeTarget(array_merge($request->validated(), [
            'created_by_employee_id' => $request->user()->employee->id,
        ]));

        return back()->with('success', 'Target employee berhasil disimpan.');
    }

    public function setPositionDefault(StoreMarketingTargetDefaultRequest $request): RedirectResponse
    {
        $this->repository->setPositionDefault(array_merge($request->validated(), [
            'created_by_employee_id' => $request->user()->employee->id,
        ]));

        return back()->with('success', 'Default target posisi berhasil disimpan.');
    }
}
