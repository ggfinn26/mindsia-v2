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
        MemberSupportTicketReply::create(array_merge(
            $request->validated(),
            ['member_support_ticket_id' => $ticket->id],
        ));

        return redirect()->back()->with('success', 'Balasan berhasil dikirim.');
    }
}
