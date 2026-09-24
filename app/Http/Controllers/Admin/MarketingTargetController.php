<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\StoreMarketingTargetDefaultRequest;
use App\Http\Requests\Marketing\StoreMarketingTargetRequest;
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

        $employees = Employee::where('is_active', true)
            ->with('currentStatus.position')
            ->orderBy('full_name')
            ->get();

        $employeeTargets = MarketingTarget::where('period_month', $month)
            ->where('period_year', $year)
            ->get()
            ->keyBy('employee_id');

        return view('marketing.target.index', compact(
            'month', 'year', 'positions', 'positionTargets', 'employees', 'employeeTargets',
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
