<?php

namespace App\Repositories\Marketing;

use App\Models\Employee;
use App\Models\Socialization;
use App\Services\Marketing\MarketingSnapshotService;
use Illuminate\Pagination\LengthAwarePaginator;

class SocializationRepository
{
    public function __construct(
        private readonly MarketingSnapshotService $snapshotService,
    ) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return Socialization::with(['branch', 'area', 'institution', 'createdBy'])
            ->when(! empty($filters['branch_ids']), fn ($q) => $q->whereIn('branch_id', $filters['branch_ids']))
            ->when(! empty($filters['branch_id']), fn ($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(! empty($filters['area_id']), fn ($q) => $q->where('area_id', $filters['area_id']))
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
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
        abort_unless(
            in_array($socialization->status, [Socialization::STATUS_DRAFT, Socialization::STATUS_SCHEDULED], true),
            422,
            'Hanya sosialisasi draft atau terjadwal yang bisa dibatalkan.'
        );

        $socialization->update(['status' => Socialization::STATUS_CANCELLED]);
    }

    /**
     * Pembatalan pengecualian untuk sosialisasi yang sudah Menunggu Validasi
     * (mis. kendala di lapangan yang lupa dibatalkan). Dijaga permission terpisah.
     */
    public function forceCancel(Socialization $socialization): void
    {
        abort_unless(
            $socialization->status === Socialization::STATUS_AWAITING_VALIDATION,
            422,
            'Batalkan paksa hanya untuk sosialisasi berstatus Menunggu Validasi.'
        );

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

        $scheduleDate = $socialization->schedules()->min('schedule_date');
        $month = $scheduleDate ? (int) date('n', strtotime($scheduleDate)) : now()->month;
        $year = $scheduleDate ? (int) date('Y', strtotime($scheduleDate)) : now()->year;

        foreach ($socialization->employeeSocializations as $es) {
            $employee = Employee::with('branch.area.region')->find($es->employee_id);
            if ($employee) {
                $this->snapshotService->append($employee, $month, $year);
            }
        }
    }
}
