<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class WorkScheduleRepository
{
    /**
     * Resolve active schedule for employee on a date.
     * Priority: employee > position > role.
     */
    public function resolveForEmployee(int $employeeId, string $date): ?WorkScheduleRule
    {
        $employee = Employee::with(['currentStatus.position'])->find($employeeId);

        return $employee ? ($this->resolveForPeriod($employee, $date, $date)[$date] ?? null) : null;
    }

    /**
     * Jadwal efektif per tanggal dalam rentang, dengan sedikit query (bukan satu set query per hari).
     * Per tanggal: assignment level pegawai, lalu posisi, lalu role. Assignment yang aturannya tidak
     * berlaku di hari itu (di luar work_days) dilewati dan jatuh ke level berikutnya.
     * Dengan $respectWorkDays = false, aturan dari penugasan yang berlaku dikembalikan tanpa memeriksa hari kerjanya
     * (untuk menanyakan "aturan apa yang berlaku", bukan "apakah hari ini hari kerja").
     *
     * @return array<string, WorkScheduleRule> kunci Y-m-d; tanggal tanpa jadwal tidak ada di hasil
     */
    public function resolveForPeriod(Employee $employee, string $from, string $to, bool $respectWorkDays = true): array
    {
        $employee->loadMissing('currentStatus.position');

        $levels = array_filter([
            'App\Models\Employee' => $employee->id,
            'App\Models\Position' => $employee->currentStatus?->position_id,
            Role::class => $employee->currentStatus?->position?->role_id,
        ]);

        $assignments = WorkScheduleAssignment::where('is_active', true)
            ->where('effective_start_date', '<=', $to)
            ->where(fn ($q) => $q->whereNull('effective_end_date')->orWhere('effective_end_date', '>=', $from))
            ->where(function ($q) use ($levels) {
                foreach ($levels as $type => $id) {
                    $q->orWhere(fn ($w) => $w->where('assignable_type', $type)->where('assignable_id', $id));
                }
            })
            ->with('workScheduleRule.workDays')
            ->orderBy('id')
            ->get();

        $resolved = [];

        for ($day = Carbon::parse($from); $day->lte(Carbon::parse($to)); $day->addDay()) {
            $date = $day->toDateString();

            foreach ($levels as $type => $id) {
                $assignment = $assignments->first(fn ($a) => $a->assignable_type === $type
                    && (int) $a->assignable_id === (int) $id
                    && $a->effective_start_date->toDateString() <= $date
                    && ($a->effective_end_date === null || $a->effective_end_date->toDateString() >= $date));

                if ($assignment?->workScheduleRule && (! $respectWorkDays || $assignment->workScheduleRule->isActiveOnDate($date))) {
                    $resolved[$date] = $assignment->workScheduleRule;

                    break;
                }
            }
        }

        return $resolved;
    }

    /** True jika employee punya assignment aktif (tanpa cek hari kerja). */
    public function hasAnyActiveAssignment(int $employeeId, string $date): bool
    {
        $employee = Employee::with(['currentStatus.position'])->find($employeeId);
        if (! $employee) {
            return false;
        }

        $positionId = $employee->currentStatus?->position_id;
        $roleId = $employee->currentStatus?->position?->role_id;

        foreach ([
            ['App\Models\Employee', $employeeId],
            $positionId ? ['App\Models\Position', $positionId] : null,
            $roleId ? [Role::class, $roleId] : null,
        ] as $pair) {
            if (! $pair) {
                continue;
            }
            [$type, $id] = $pair;
            $exists = WorkScheduleAssignment::where('assignable_type', $type)
                ->where('assignable_id', $id)
                ->where('is_active', true)
                ->where('effective_start_date', '<=', $date)
                ->where(fn ($q) => $q->whereNull('effective_end_date')->orWhere('effective_end_date', '>=', $date))
                ->exists();
            if ($exists) {
                return true;
            }
        }

        return false;
    }
}
