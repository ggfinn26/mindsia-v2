<?php

namespace App\Repositories\Member;

use App\Models\MemberData;
use App\Models\MemberSupportTicket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MemberSupportTicketRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return MemberSupportTicket::with(['branch', 'member', 'assignedEmployee'])
            ->when($filters['branch_id'] ?? null, fn ($q, $v) => $q->where('branch_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['category'] ?? null, fn ($q, $v) => $q->where('category', $v))
            ->orderByDesc('created_at')
            ->paginate(25);
    }

    public function create(MemberData $member, array $data): MemberSupportTicket
    {
        return DB::transaction(function () use ($member, $data) {
            $data['member_id'] = $member->id;
            $data['ticket_number'] = $this->generateTicketNumber();
            $data['status'] = 'open';

            return MemberSupportTicket::create($data);
        });
    }

    public function changeStatus(MemberSupportTicket $ticket, string $newStatus): void
    {
        $updates = ['status' => $newStatus];

        if ($newStatus === 'resolved') {
            $updates['resolved_at'] = now();
        }

        $ticket->update($updates);
    }

    public function assign(MemberSupportTicket $ticket, int $employeeId): void
    {
        $ticket->update(['assigned_employee_id' => $employeeId]);
    }

    private function generateTicketNumber(): string
    {
        $today = Carbon::today()->format('Ymd');
        // lockForUpdate prevents duplicate numbers under concurrent requests
        $count = MemberSupportTicket::whereDate('created_at', today())->lockForUpdate()->count() + 1;

        return sprintf('TICK-%s-%03d', $today, $count);
    }
}
