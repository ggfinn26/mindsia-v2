<?php

namespace App\Repositories\Facility;

use App\Models\FacilityTicket;
use App\Models\FacilityTicketStatusHistory;
use Illuminate\Database\Eloquent\Collection;

class FacilityTicketRepository
{
    public function find(int $id): FacilityTicket
    {
        return FacilityTicket::with(['branch', 'createdBy', 'assignedTo', 'statusHistories', 'attachments'])
            ->findOrFail($id);
    }

    public function byBranch(int $branchId, ?string $status = null): Collection
    {
        return FacilityTicket::where('branch_id', $branchId)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->get();
    }

    public function create(array $data): FacilityTicket
    {
        return FacilityTicket::create($data);
    }

    public function update(FacilityTicket $ticket, array $data): FacilityTicket
    {
        $ticket->update($data);

        return $ticket;
    }

    public function changeStatus(FacilityTicket $ticket, string $toStatus, ?int $employeeId, ?string $notes): FacilityTicket
    {
        $fromStatus = $ticket->status;
        $ticket->update(['status' => $toStatus]);

        FacilityTicketStatusHistory::create([
            'facility_ticket_id' => $ticket->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by_employee_id' => $employeeId,
            'notes' => $notes,
            'changed_at' => now(),
        ]);

        return $ticket;
    }

    public function assign(FacilityTicket $ticket, ?int $employeeId): FacilityTicket
    {
        $ticket->update(['assigned_to_employee_id' => $employeeId]);

        return $ticket;
    }
}
