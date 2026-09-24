<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\UpdateProspectiveMemberStatusRequest;
use App\Models\ProspectiveMember;
use App\Repositories\Marketing\ProspectiveMemberRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyProspectiveMemberController extends Controller
{
    public function __construct(
        private readonly ProspectiveMemberRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 403);

        $filters = array_merge(
            $request->only(['status', 'search', 'sort', 'dir']),
            ['employee_id' => $employee->id],
        );

        return view('marketing.my-prospective-member.index', [
            'leads' => $this->repository->paginate($filters),
        ]);
    }

    public function updateStatus(UpdateProspectiveMemberStatusRequest $request, ProspectiveMember $myProspectiveMember): RedirectResponse
    {
        abort_unless($myProspectiveMember->captured_by_employee_id === $request->user()->employee->id, 403);

        $this->repository->updateStatus(
            $myProspectiveMember,
            $request->validated('status'),
            $request->user()->employee->id,
            $request->validated('change_reason'),
        );

        return back()->with('success', 'Status berhasil diperbarui.');
    }
}
