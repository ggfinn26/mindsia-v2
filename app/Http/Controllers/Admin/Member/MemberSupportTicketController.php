<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\AssignMemberSupportTicketRequest;
use App\Http\Requests\Member\ChangeMemberSupportTicketStatusRequest;
use App\Http\Requests\Member\StoreMemberSupportTicketRequest;
use App\Models\MemberData;
use App\Models\MemberSupportTicket;
use App\Repositories\Member\MemberSupportTicketRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberSupportTicketController extends Controller
{
    public function __construct(
        private readonly MemberSupportTicketRepository $repo,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['branch_id', 'status', 'category']);

        return view('admin.member.support-tickets.index', [
            'tickets' => $this->repo->paginate($filters),
        ]);
    }

    public function store(StoreMemberSupportTicketRequest $request): RedirectResponse
    {
        $member = MemberData::findOrFail($request->validated('member_id'));
        $ticket = $this->repo->create($member, $request->validated());

        return redirect()->route('support-tickets.show', $ticket)->with('success', 'Tiket berhasil dibuat.');
    }

    public function show(MemberSupportTicket $ticket): View
    {
        $ticket->load(['branch', 'member', 'assignedEmployee', 'replies.employee', 'replies.member', 'statusHistories']);

        return view('admin.member.support-tickets.show', compact('ticket'));
    }

    public function changeStatus(ChangeMemberSupportTicketStatusRequest $request, MemberSupportTicket $ticket): RedirectResponse
    {
        $this->repo->changeStatus($ticket, $request->validated('new_status'));

        return redirect()->back()->with('success', 'Status tiket berhasil diperbarui.');
    }

    public function assign(AssignMemberSupportTicketRequest $request, MemberSupportTicket $ticket): RedirectResponse
    {
        $this->repo->assign($ticket, $request->validated('employee_id'));

        return redirect()->back()->with('success', 'Tiket berhasil di-assign.');
    }
}
