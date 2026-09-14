<?php

namespace App\Repositories\Marketing;

use App\Models\Socialization;
use Illuminate\Pagination\LengthAwarePaginator;

class SocializationRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return Socialization::with(['branch', 'area', 'institution', 'createdBy'])
            ->when(isset($filters['branch_id']), fn ($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(isset($filters['area_id']), fn ($q) => $q->where('area_id', $filters['area_id']))
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest()
            ->paginate(20);
    }

    public function find(int $id): Socialization
    {
        return Socialization::with([
            'branch', 'area', 'institution', 'createdBy',
            'schedules', 'employeeSocializations.employee', 'prospectiveMembers',
        ])->findOrFail($id);
    }

    public function create(array $data): Socialization
    {
        return Socialization::create($data);
    }

    public function update(Socialization $socialization, array $data): Socialization
    {
        $socialization->update($data);

        return $socialization;
    }

    public function schedule(Socialization $socialization): Socialization
    {
        abort_unless($socialization->status === Socialization::STATUS_DRAFT, 422, 'Hanya draft yang bisa dijadwalkan.');

        $socialization->update(['status' => Socialization::STATUS_SCHEDULED]);

        return $socialization;
    }

    public function cancel(Socialization $socialization): void
    {
        abort_if($socialization->status === Socialization::STATUS_COMPLETED, 422, 'Sosialisasi selesai tidak bisa dibatalkan.');

        $socialization->update(['status' => Socialization::STATUS_CANCELLED]);
    }

    public function updatePartnerFeeStatus(Socialization $socialization, string $status, ?string $paidAt = null): Socialization
    {
        $socialization->update([
            'partner_fee_status' => $status,
            'partner_fee_paid_at' => $paidAt,
        ]);

        return $socialization;
    }

    public function assignEmployee(Socialization $socialization, int $employeeId): void
    {
        $socialization->employeeSocializations()->firstOrCreate([
            'employee_id' => $employeeId,
        ], [
            'classes_obtained' => 0,
            'status' => 'assigned',
        ]);
    }

    public function completeWithReport(Socialization $socialization, array $employeeResults): void
    {
        // $employeeResults: [['employee_socialization_id' => X, 'classes_obtained' => N], ...]
        foreach ($employeeResults as $result) {
            $es = $socialization->employeeSocializations()->findOrFail($result['employee_socialization_id']);
            $es->update([
                'classes_obtained' => $result['classes_obtained'],
                'status' => 'confirmed',
            ]);

            $prospectiveCount = $socialization->prospectiveMembers()
                ->where('captured_by_employee_id', $es->employee_id)
                ->count();

            $es->history()->updateOrCreate(
                ['employee_socialization_id' => $es->id],
                [
                    'employee_id' => $es->employee_id,
                    'socialization_id' => $socialization->id,
                    'employee_name_snapshot' => $es->employee->full_name,
                    'socialization_date_snapshot' => $socialization->schedules()->min('schedule_date'),
                    'location_name_snapshot' => $socialization->location_name,
                    'classes_obtained' => $result['classes_obtained'],
                    'prospective_members_count' => $prospectiveCount,
                    'snapshot_at' => now(),
                ]
            );
        }

        $socialization->update(['status' => Socialization::STATUS_COMPLETED]);
    }
}
