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

        MemberSupportTicketReply::create([
            'member_support_ticket_id' => $ticket->id,
            'reply' => $request->validated('reply'),
            'employee_id' => $employeeId ?: null,
            'member_id' => $employeeId ? null : $memberId,
        ]);

        return redirect()->back()->with('success', 'Balasan berhasil dikirim.');
    }
}
