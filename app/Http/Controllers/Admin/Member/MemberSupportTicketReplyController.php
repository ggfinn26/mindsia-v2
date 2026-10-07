<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberSupportTicketReplyRequest;
use App\Models\MemberSupportTicket;
use App\Models\MemberSupportTicketReply;
use Illuminate\Http\RedirectResponse;

class MemberSupportTicketReplyController extends Controller
{
    public function store(StoreMemberSupportTicketReplyRequest $request, MemberSupportTicket $ticket): RedirectResponse
    {
        $employeeId = auth('web')->user()?->employee_id;
        $memberId = auth('member')->id();

        // Allow employee to reply on behalf of the ticket owner only when they
        // have manage permission and the member_id matches the ticket owner.
        // Prevents identity spoofing to arbitrary members.
        $requestedMemberId = $request->validated('member_id');
        if ($requestedMemberId && $request->user()->can('member.support.manage') && (int) $requestedMemberId === (int) $ticket->member_id) {
            $memberId = $requestedMemberId;
            $employeeId = null;
        }

        MemberSupportTicketReply::create([
            'member_support_ticket_id' => $ticket->id,
            'reply' => $request->validated('reply'),
            'employee_id' => $memberId ? null : ($employeeId ?: null),
            'member_id' => $memberId ?: null,
        ]);

        return redirect()->back()->with('success', 'Balasan berhasil dikirim.');
    }
}
