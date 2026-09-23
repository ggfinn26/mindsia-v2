<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\AssignEmployeeSocializationRequest;
use App\Http\Requests\Marketing\StoreSocializationRequest;
use App\Http\Requests\Marketing\UpdateSocializationRequest;
use App\Models\Branch;
use App\Models\Institution;
use App\Models\Socialization;
use App\Repositories\Marketing\SocializationRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SocializationController extends Controller
{
    public function __construct(
        private readonly SocializationRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->canAny(['marketing.socialization.create', 'marketing.socialization.update']), 403);

        return view('marketing.socialization.index', [
            'socializations' => $this->repository->paginate($request->only(['branch_id', 'area_id', 'status'])),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('marketing.socialization.create'), 403);

        $employee = $request->user()->employee;

        $branches = match (true) {
            (bool) $employee->branch_id => Branch::where('id', $employee->branch_id)->get(),
            (bool) $employee->area_id => Branch::active()->where('areas_id', $employee->area_id)->orderBy('branch_name')->get(),
            (bool) $employee->region_id => Branch::active()->whereHas('area', fn ($q) => $q->where('region_id', $employee->region_id))->orderBy('branch_name')->get(),
            default => Branch::active()->orderBy('branch_name')->get(),
        };

        return view('marketing.socialization.create', [
            'employee' => $employee,
            'branches' => $branches,
            'institutions' => Institution::orderBy('institution_name')->get(),
        ]);
    }

    public function store(StoreSocializationRequest $request): RedirectResponse
    {
        $employee = $request->user()->employee;
        $status = $request->boolean('schedule_now') ? Socialization::STATUS_SCHEDULED : Socialization::STATUS_DRAFT;
        $base = array_merge(
            collect($request->validated())->except(['branch_ids'])->all(),
            [
                'area_id' => $employee->area_id,
                'created_by_employee_id' => $employee->id,
                'status' => $status,
            ]
        );

        // Branch-level employee: single branch from profile
        if ($employee->branch_id) {
            $socialization = $this->repository->create(array_merge($base, ['branch_id' => $employee->branch_id]));

            return redirect()->route('socializations.show', $socialization)->with('success', 'Sosialisasi berhasil dibuat.');
        }

        // Area/region/HQ: one socialization per selected branch
        $branchIds = $request->validated('branch_ids') ?? [];
        abort_unless(count($branchIds) > 0, 422, 'Pilih minimal 1 cabang.');

        $created = collect($branchIds)->map(
            fn ($branchId) => $this->repository->create(array_merge($base, ['branch_id' => $branchId]))
        );

        if ($created->count() === 1) {
            return redirect()->route('socializations.show', $created->first())->with('success', 'Sosialisasi berhasil dibuat.');
        }

        return redirect()->route('socializations.index')->with('success', "{$created->count()} sosialisasi berhasil dibuat.");
    }

    public function show(Request $request, Socialization $socialization): View
    {
        abort_unless($request->user()->canAny(['marketing.socialization.create', 'marketing.socialization.update']), 403);

        return view('marketing.socialization.show', [
            'socialization' => $this->repository->find($socialization->id),
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

    public function assignEmployee(AssignEmployeeSocializationRequest $request, Socialization $socialization): RedirectResponse
    {
        $this->repository->assignEmployee($socialization, $request->validated('employee_id'));

        return back()->with('success', 'Marketer berhasil ditugaskan.');
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
