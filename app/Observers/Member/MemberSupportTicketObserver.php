<?php

namespace App\Observers\Member;

use App\Models\MemberSupportTicket;
use App\Models\MemberSupportTicketStatusHistory;

class MemberSupportTicketObserver
{
    public function updating(MemberSupportTicket $ticket): void
    {
        if (! $ticket->isDirty('status')) {
            return;
        }

        MemberSupportTicketStatusHistory::create([
            'member_support_ticket_id' => $ticket->id,
            'from_status' => $ticket->getOriginal('status'),
            'to_status' => $ticket->status,
            'changed_by_employee_id' => auth('web')->id(),
            'changed_by_member_id' => auth('member')->id(),
            'changed_at' => now(),
        ]);
    }
}
