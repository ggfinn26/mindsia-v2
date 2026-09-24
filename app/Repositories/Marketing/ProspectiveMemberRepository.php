<?php

namespace App\Repositories\Marketing;

use App\Models\ProspectiveMember;
use Illuminate\Pagination\LengthAwarePaginator;

class ProspectiveMemberRepository
{
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $sort = in_array($filters['sort'] ?? '', ['full_name', 'status', 'created_at']) ? $filters['sort'] : 'created_at';
        $dir = ($filters['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return ProspectiveMember::with(['capturedBy', 'socialization.branch', 'institution'])
            ->when(! empty($filters['employee_id']), fn ($q) => $q->where('captured_by_employee_id', $filters['employee_id']))
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(! empty($filters['socialization_id']), fn ($q) => $q->where('socialization_id', $filters['socialization_id']))
            ->when(! empty($filters['branch_id']), fn ($q) => $q->whereHas('capturedBy', fn ($e) => $e->where('branch_id', $filters['branch_id'])))
            ->when(! empty($filters['search']), fn ($q) => $q->where(fn ($s) => $s
                ->where('full_name', 'like', '%'.$filters['search'].'%')
                ->orWhere('whatsapp_number', 'like', '%'.$filters['search'].'%')
            ))
            ->orderBy($sort, $dir)
            ->paginate($perPage);
    }

    public function find(int $id): ProspectiveMember
    {
        return ProspectiveMember::with([
            'capturedBy', 'socialization', 'institution',
            'statusHistories.changedBy',
        ])->findOrFail($id);
    }

    public function create(array $data): ProspectiveMember
    {
        $lead = ProspectiveMember::create($data);

        // seed initial status history
        $lead->statusHistories()->create([
            'previous_status' => null,
            'new_status' => $lead->status,
            'changed_by_employee_id' => $data['captured_by_employee_id'] ?? null,
            'change_reason' => null,
        ]);

        return $lead;
    }

    public function updateStatus(ProspectiveMember $lead, string $newStatus, int $changedById, ?string $reason = null): ProspectiveMember
    {
        $previousStatus = $lead->status;

        $lead->update(['status' => $newStatus]);

        $lead->statusHistories()->create([
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'changed_by_employee_id' => $changedById,
            'change_reason' => $reason,
        ]);

        return $lead;
    }

    public function logFollowUp(ProspectiveMember $lead, int $employeeId, string $note): void
    {
        $lead->statusHistories()->create([
            'previous_status' => $lead->status,
            'new_status' => $lead->status,
            'changed_by_employee_id' => $employeeId,
            'change_reason' => $note,
        ]);
    }

    public function convertToMember(ProspectiveMember $lead, int $memberId, int $changedById): ProspectiveMember
    {
        return $this->updateStatus($lead, ProspectiveMember::STATUS_FIXED, $changedById);
    }
}
