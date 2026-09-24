<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\StoreProspectiveMemberRequest;
use App\Http\Requests\Marketing\UpdateProspectiveMemberStatusRequest;
use App\Models\Branch;
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
            'marketing.prospective_member.create',
            'marketing.prospective_member.update',
            'marketing.prospective.view_area',
            'marketing.prospective.view_national',
        ]), 403);

        $canViewWide = $request->user()->canAny(['marketing.prospective.view_area', 'marketing.prospective.view_national']);
        $currentView = $canViewWide ? $request->input('view', 'mine') : 'mine';

        $filters = $request->only(['status', 'socialization_id', 'branch_id', 'search', 'sort', 'dir']);

        if (! $canViewWide || $currentView === 'mine') {
            $filters['employee_id'] = $request->user()->employee->id;
            unset($filters['branch_id']);
        }

        $branches = $canViewWide
            ? Branch::active()->orderBy('branch_name')->get(['id', 'branch_name'])
            : collect();

        return view('marketing.prospective-member.index', [
            'leads' => $this->repository->paginate($filters),
            'canViewWide' => $canViewWide,
            'currentView' => $currentView,
            'branches' => $branches,
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
