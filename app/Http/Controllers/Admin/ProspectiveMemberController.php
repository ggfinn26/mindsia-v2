<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\StoreProspectiveMemberRequest;
use App\Http\Requests\Marketing\UpdateProspectiveMemberStatusRequest;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeSocialization;
use App\Models\ProspectiveMember;
use App\Repositories\Marketing\ProspectiveMemberRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProspectiveMemberController extends Controller
{
    public function __construct(
        private readonly ProspectiveMemberRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->canAny([
            'marketing.prospective.view_area',
            'marketing.prospective.view_national',
            'marketing.prospective_member.view',
            'marketing.prospective_member.create',
        ]), 403);

        $canViewNational = $request->user()->can('marketing.prospective.view_national');
        $filters = $request->only(['status', 'branch_id', 'employee_id', 'search', 'sort', 'dir']);
        $branches = Branch::active()->orderBy('branch_name')->get(['id', 'branch_name']);
        $employees = Employee::where('is_active', true)->orderBy('full_name')->get(['id', 'full_name']);

        return view('marketing.prospective-member.index', [
            'leads' => $this->repository->paginate($filters),
            'canViewNational' => $canViewNational,
            'branches' => $branches,
            'employees' => $employees,
        ]);
    }

    public function create(): View
    {
        return view('marketing.prospective-member.create');
    }

    public function store(StoreProspectiveMemberRequest $request): RedirectResponse
    {
        $lead = $this->repository->create(array_merge($request->validated(), [
            'captured_by_employee_id' => $request->user()->employee->id,
            'status' => $request->validated('status') ?? ProspectiveMember::STATUS_ALMOST,
        ]));

        if ($lead->socialization_id) {
            if (! $request->user()->can('marketing.socialization.update') && $request->user()->employee) {
                $es = EmployeeSocialization::where('socialization_id', $lead->socialization_id)
                    ->where('employee_id', $request->user()->employee->id)
                    ->first();
                if ($es) {
                    return redirect()->route('my-socializations.show', $es)
                        ->with('success', 'Lead berhasil ditambahkan.');
                }
            }

            return redirect()->route('socializations.show', $lead->socialization_id)
                ->with('success', 'Lead berhasil ditambahkan.');
        }

        return redirect()->route('prospective-members.show', $lead)->with('success', 'Lead berhasil ditambahkan.');
    }

    public function show(Request $request, ProspectiveMember $prospectiveMember): View
    {
        if (! $request->user()->can('marketing.prospective.view_area')
            && ! $request->user()->can('marketing.prospective.view_national')) {
            abort_unless($prospectiveMember->captured_by_employee_id === $request->user()->employee->id, 403);
        }

        return view('marketing.prospective-member.show', [
            'lead' => $this->repository->find($prospectiveMember->id),
        ]);
    }

    public function edit(Request $request, ProspectiveMember $prospectiveMember): View
    {
        if (! $request->user()->can('marketing.prospective.view_area')
            && ! $request->user()->can('marketing.prospective.view_national')) {
            abort_unless($prospectiveMember->captured_by_employee_id === $request->user()->employee->id, 403);
        }

        return view('marketing.prospective-member.edit', [
            'lead' => $this->repository->find($prospectiveMember->id),
        ]);
    }

    public function update(Request $request, ProspectiveMember $prospectiveMember): RedirectResponse
    {
        if (! $request->user()->can('marketing.prospective.view_area')
            && ! $request->user()->can('marketing.prospective.view_national')) {
            abort_unless($prospectiveMember->captured_by_employee_id === $request->user()->employee->id, 403);
        }

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'min:8', 'max:15'],
            'email' => ['nullable', 'email', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:L,P'],
        ]);

        $prospectiveMember->update($data);

        return redirect()->route('prospective-members.show', $prospectiveMember)->with('success', 'Data lead berhasil diperbarui.');
    }

    public function updateStatus(UpdateProspectiveMemberStatusRequest $request, ProspectiveMember $prospectiveMember): RedirectResponse
    {
        if (! $request->user()->can('marketing.prospective.view_area')
            && ! $request->user()->can('marketing.prospective.view_national')) {
            abort_unless($prospectiveMember->captured_by_employee_id === $request->user()->employee->id, 403);
        }

        $this->repository->updateStatus(
            $prospectiveMember,
            $request->validated('status'),
            $request->user()->employee->id,
            $request->validated('change_reason'),
        );

        return back()->with('success', 'Status lead berhasil diperbarui.');
    }

    public function storeFollowUp(Request $request, ProspectiveMember $prospectiveMember): RedirectResponse
    {
        $request->validate(['note' => ['required', 'string', 'max:1000']]);

        if (! $request->user()->can('marketing.prospective.view_area')
            && ! $request->user()->can('marketing.prospective.view_national')) {
            abort_unless($prospectiveMember->captured_by_employee_id === $request->user()->employee->id, 403);
        }

        $this->repository->logFollowUp(
            $prospectiveMember,
            $request->user()->employee->id,
            $request->input('note'),
        );

        return back()->with('success', 'Follow-up berhasil dicatat.');
    }
}
