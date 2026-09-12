<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberDataRequest;
use App\Http\Requests\Member\UpdateMemberDataRequest;
use App\Models\MemberData;
use App\Repositories\Member\MemberDataRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberDataController extends Controller
{
    public function __construct(
        private readonly MemberDataRepository $repo,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['institution_id', 'program_id', 'search']);

        return view('admin.member.index', [
            'members' => $this->repo->paginate($filters),
        ]);
    }

    public function create(): View
    {
        return view('admin.member.create');
    }

    public function store(StoreMemberDataRequest $request): RedirectResponse
    {
        $member = $this->repo->create($request->validated());

        return redirect()->route('members.show', $member)->with('success', 'Data member berhasil ditambahkan.');
    }

    public function show(MemberData $member): View
    {
        return view('admin.member.show', [
            'member' => $this->repo->findWithDetails($member->id),
        ]);
    }

    public function edit(MemberData $member): View
    {
        return view('admin.member.edit', compact('member'));
    }

    public function update(UpdateMemberDataRequest $request, MemberData $member): RedirectResponse
    {
        $this->repo->update($member, $request->validated());

        return redirect()->route('members.show', $member)->with('success', 'Data member berhasil diperbarui.');
    }

    public function destroy(MemberData $member): RedirectResponse
    {
        $this->repo->delete($member);

        return redirect()->route('members.index')->with('success', 'Data member berhasil dihapus.');
    }
}
