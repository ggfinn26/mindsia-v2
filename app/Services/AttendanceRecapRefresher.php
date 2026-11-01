<?php

namespace App\Services;

use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use App\Repositories\AttendanceRecapRepository;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

/**
 * Rekap bulanan menyimpan hari terjadwal dan hari libur, jadi harus dihitung ulang saat data yang
 * mempengaruhinya berubah (libur, penugasan jadwal, aturan jadwal, setting wajib absen). Hanya rekap yang
 * SUDAH ada yang dihitung ulang; pegawai tanpa rekap di bulan itu tidak dibuatkan baris baru.
 */
class AttendanceRecapRefresher
{
    public function __construct(private readonly AttendanceRecapRepository $recapRepo) {}

    /**
     * @param  list<int>|null  $employeeIds  null = semua pegawai
     * @return int jumlah rekap yang dihitung ulang
     */
    public function refreshRange(\DateTimeInterface|string $from, \DateTimeInterface|string|null $to, ?array $employeeIds = null): int
    {
        $month = Carbon::parse($from)->startOfMonth();
        $last = Carbon::parse($to ?? now())->startOfMonth();
        $count = 0;

        for (; $month->lte($last); $month->addMonth()) {
            $ids = EmployeeAttendanceMonthlyRecap::where('period_year', $month->year)
                ->where('period_month', $month->month)
                ->when($employeeIds !== null, fn ($q) => $q->whereIn('employee_id', $employeeIds))
                ->pluck('employee_id');

            foreach ($ids as $id) {
                $this->recapRepo->upsertForEmployee($id, $month->copy());
                $count++;
            }
        }

        return $count;
    }

    /** Setting tanpa tanggal berlaku efektif sejak sekarang, jadi hanya bulan berjalan yang dihitung ulang. */
    public function refreshCurrentMonth(?array $employeeIds = null): int
    {
        return $this->refreshRange(now(), now(), $employeeIds);
    }

    /** @param  array<string, mixed>  $original  atribut sebelum diubah (kosong bila baru dibuat) */
    public function refreshForAssignment(WorkScheduleAssignment $assignment, array $original = []): void
    {
        $this->refreshAssignmentState(
            $assignment->assignable_type, (int) $assignment->assignable_id,
            $assignment->effective_start_date, $assignment->effective_end_date,
        );

        if ($original) {
            $this->refreshAssignmentState(
                $original['assignable_type'] ?? $assignment->assignable_type,
                (int) ($original['assignable_id'] ?? $assignment->assignable_id),
                $original['effective_start_date'] ?? $assignment->effective_start_date,
                $original['effective_end_date'] ?? null,
            );
        }
    }

    public function refreshForRule(WorkScheduleRule $rule): void
    {
        foreach ($rule->assignments()->get() as $assignment) {
            $this->refreshForAssignment($assignment);
        }
    }

    private function refreshAssignmentState(string $type, int $id, mixed $start, mixed $end): void
    {
        $this->refreshRange($start, $end, $this->employeeIdsFor($type, $id));
    }

    /** @return list<int> */
    public function employeeIdsFor(string $type, int $id): array
    {
        return match ($type) {
            'App\Models\Employee' => [$id],
            'App\Models\Position' => $this->employeesInPositions([$id]),
            Role::class, 'Spatie\Permission\Models\Role' => $this->employeesInPositions(Position::where('role_id', $id)->pluck('id')->all()),
            default => [],
        };
    }

    /**
     * @param  list<int>  $positionIds
     * @return list<int>
     */
    public function employeesInPositions(array $positionIds): array
    {
        return EmploymentStatus::whereIn('position_id', $positionIds)->pluck('employees_id')->unique()->values()->all();
    }
}
