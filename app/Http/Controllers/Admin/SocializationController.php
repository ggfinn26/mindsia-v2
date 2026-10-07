<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\AssignEmployeeSocializationRequest;
use App\Http\Requests\Marketing\StoreSocializationRequest;
use App\Http\Requests\Marketing\UpdateSocializationRequest;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeSocialization;
use App\Models\Institution;
use App\Models\Socialization;
use App\Repositories\Marketing\ProspectiveMemberRepository;
use App\Repositories\Marketing\SocializationRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SocializationController extends Controller
{
    public function __construct(
        private readonly SocializationRepository $repository,
        private readonly ProspectiveMemberRepository $pmRepository,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->canAny(['marketing.socialization.create', 'marketing.socialization.update']), 403);

        $employee = $request->user()->employee;
        $canViewAll = $request->user()->can('marketing.socialization.update');
        $currentView = $canViewAll ? $request->input('view', 'mine') : 'mine';

        $filters = $request->only(['status', 'branch_id', 'area_id']);

        if ($currentView === 'mine' && $employee) {
            // Socializations are area-based (branch_id = null). Scope by area_id.
            $areaId = $employee->area_id
                ?? ($employee->branch_id ? Branch::find($employee->branch_id)?->areas_id : null);

            if ($areaId) {
                $filters['area_id'] = $areaId;
            }
        }

        return view('marketing.socialization.index', [
            'socializations' => $this->repository->paginate($filters),
            'canViewAll' => $canViewAll,
            'currentView' => $currentView,
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('marketing.socialization.create'), 403);

        $employee = $request->user()->employee;

        $area = $employee->area
            ?? ($employee->branch_id ? Branch::with('area')->find($employee->branch_id)?->area : null);

        $institutions = Institution::orderBy('institution_name')
            ->when($area?->region_id, fn ($q) => $q->where('regions_id', $area->region_id))
            ->get(['id', 'institution_name']);

        $eligibleEmployees = $area
            ? Employee::where('is_active', true)
                ->where(fn ($q) => $q
                    ->where('area_id', $area->id)
                    ->orWhereHas('branch', fn ($b) => $b->where('areas_id', $area->id))
                )->orderBy('full_name')->get(['id', 'full_name'])
            : Employee::where('is_active', true)->orderBy('full_name')->get(['id', 'full_name']);

        return view('marketing.socialization.create', compact('employee', 'institutions', 'area', 'eligibleEmployees'));
    }

    public function store(StoreSocializationRequest $request): RedirectResponse
    {
        $employee = $request->user()->employee;
        $status = $request->boolean('schedule_now') ? Socialization::STATUS_SCHEDULED : Socialization::STATUS_DRAFT;

        $areaId = $employee->area_id
            ?? ($employee->branch_id ? Branch::find($employee->branch_id)?->areas_id : null);

        $validated = $request->validated();

        $socialization = $this->repository->create(array_merge($validated, [
            'area_id' => $areaId,
            'created_by_employee_id' => $employee->id,
            'status' => $status,
        ]));

        return redirect()->route('socializations.show', $socialization)
            ->with('success', 'Sosialisasi berhasil dibuat.');
    }

    public function show(Request $request, Socialization $socialization): View
    {
        abort_unless($request->user()->canAny(['marketing.socialization.create', 'marketing.socialization.update']), 403);

        $soc = $this->repository->find($socialization->id);
        $employee = $request->user()->employee;

        $myEs = $employee
            ? $soc->employeeSocializations->firstWhere('employee_id', $employee->id)
            : null;

        $viewMode = $myEs && ! $request->user()->can('marketing.socialization.update')
            ? 'mine'
            : $request->input('view', 'full');

        $pmFilters = array_filter([
            'socialization_id' => $soc->id,
            'status' => $request->input('pm_status'),
            'search' => $request->input('pm_search'),
            'sort' => $request->input('pm_sort'),
            'dir' => $request->input('pm_dir'),
        ], fn ($v) => $v !== null && $v !== '');

        if ($viewMode === 'mine' && $employee) {
            $pmFilters['employee_id'] = $employee->id;
        }

        $leads = $this->pmRepository->paginate($pmFilters, 10);

        $eligibleEmployees = $soc->area_id
            ? Employee::whereHas('branch', fn ($q) => $q->where('areas_id', $soc->area_id))
                ->orWhere('area_id', $soc->area_id)
                ->orderBy('full_name')
                ->get(['id', 'full_name'])
            : Employee::orderBy('full_name')->get(['id', 'full_name']);

        return view('marketing.socialization.show', [
            'socialization' => $soc,
            'eligibleEmployees' => $eligibleEmployees,
            'viewMode' => $viewMode,
            'myEs' => $myEs,
            'leads' => $leads,
        ]);
    }

    public function edit(Request $request, Socialization $socialization): View
    {
        abort_unless($request->user()->can('marketing.socialization.update'), 403);
        abort_if($socialization->status === Socialization::STATUS_COMPLETED, 403);

        return view('marketing.socialization.edit', compact('socialization'));
    }

    public function update(UpdateSocializationRequest $request, Socialization $socialization): RedirectResponse
    {
        $this->repository->update($socialization, $request->validated());

        return redirect()->route('socializations.show', $socialization)->with('success', 'Sosialisasi berhasil diperbarui.');
    }

    public function schedule(Request $request, Socialization $socialization): RedirectResponse
    {
        abort_unless($request->user()->can('marketing.socialization.update'), 403);

        $this->repository->schedule($socialization);

        return redirect()->route('socializations.show', $socialization)->with('success', 'Sosialisasi dijadwalkan.');
    }

    public function cancel(Request $request, Socialization $socialization): RedirectResponse
    {
        abort_unless($request->user()->can('marketing.socialization.update'), 403);

        $this->repository->cancel($socialization);

        return redirect()->route('socializations.show', $socialization)->with('success', 'Sosialisasi dibatalkan.');
    }

    public function overrideEmployeeKelas(Request $request, Socialization $socialization, EmployeeSocialization $employeeSocialization): RedirectResponse
    {
        abort_unless($request->user()->can('marketing.socialization.update'), 403);
        abort_unless($employeeSocialization->socialization_id === $socialization->id, 404);

        $validated = $request->validate([
            'classes_obtained' => ['required', 'integer', 'min:0'],
        ]);

        $old = $employeeSocialization->classes_obtained;
        $employeeSocialization->update($validated);

        // ponytail: log override via existing history mechanism
        $employeeSocialization->history()->updateOrCreate(
            ['employee_socialization_id' => $employeeSocialization->id],
            [
                'employee_id' => $employeeSocialization->employee_id,
                'socialization_id' => $socialization->id,
                'employee_name_snapshot' => $employeeSocialization->employee->full_name,
                'socialization_date_snapshot' => $socialization->schedules()->min('schedule_date'),
                'location_name_snapshot' => $socialization->institution?->institution_name ?? $socialization->location_name,
                'classes_obtained' => $validated['classes_obtained'],
                'prospective_members_count' => $socialization->prospectiveMembers()
                    ->where('captured_by_employee_id', $employeeSocialization->employee_id)->count(),
                'snapshot_at' => now(),
            ]
        );

        return back()->with('success', "Kelas dioverride dari {$old} → {$validated['classes_obtained']}.");
    }

    public function complete(Request $request, Socialization $socialization): RedirectResponse
    {
        abort_unless($request->user()->can('marketing.socialization.update'), 403);
        abort_if($socialization->status === Socialization::STATUS_COMPLETED, 422, 'Sosialisasi sudah selesai.');

        $validated = $request->validate([
            'results' => ['required', 'array'],
            'results.*.employee_socialization_id' => ['required', 'integer'],
            'results.*.classes_obtained' => ['required', 'integer', 'min:0'],
        ]);

        $this->repository->completeWithReport($socialization, $validated['results']);

        return redirect()->route('socializations.show', $socialization)
            ->with('success', 'Sosialisasi berhasil diselesaikan.');
    }

    public function assignEmployee(AssignEmployeeSocializationRequest $request, Socialization $socialization): RedirectResponse
    {
        foreach ($request->employeeIds() as $employeeId) {
            $this->repository->assignEmployee($socialization, $employeeId);
        }

        $count = count($request->employeeIds());

        return back()->with('success', $count === 1 ? 'Marketer berhasil ditugaskan.' : "{$count} marketer berhasil ditugaskan.");
    }

    public function destroy(Request $request, Socialization $socialization): RedirectResponse
    {
        abort_unless($request->user()->can('marketing.socialization.delete'), 403);
        abort_unless($socialization->status === 'draft', 422, 'Hanya sosialisasi draft yang bisa dihapus.');

        $socialization->delete();

        return redirect()->route('socializations.index')->with('success', 'Sosialisasi berhasil dihapus.');
    }

    public function updatePartnerFee(Request $request, Socialization $socialization): RedirectResponse
    {
        abort_unless($request->user()->can('marketing.socialization.partner_fee.update'), 403);

        $validated = $request->validate([
            'partner_fee_status' => ['required', 'in:none,pending,paid'],
            'partner_fee_paid_at' => ['nullable', 'date', 'required_if:partner_fee_status,paid'],
        ]);

        $this->repository->updatePartnerFeeStatus(
            $socialization,
            $validated['partner_fee_status'],
            $validated['partner_fee_paid_at'] ?? null,
        );

        return back()->with('success', 'Status partner fee diperbarui.');
    }
}
