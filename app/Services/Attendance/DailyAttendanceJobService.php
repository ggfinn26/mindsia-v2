<?php

namespace App\Services\Attendance;

use App\Models\Employee;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\WorkScheduleRule;
use App\Repositories\AttendanceRecapRepository;
use App\Repositories\AttendanceSettingsRepository;
use App\Repositories\HolidayRepository;
use App\Repositories\WorkScheduleRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

/**
 * Dua job harian untuk absensi kerja:
 *  - auto-hadir: pegawai yang tidak wajib absen (setting ATAU aturan jadwal) dicatat hadir sejak jam mulai jadwalnya;
 *  - alpha: pegawai wajib absen yang tidak punya log sama sekali dicatat absent setelah jam selesai jadwalnya.
 * Jam mengikuti jadwal efektif pegawai (pegawai > posisi > role). Dilewati: libur nasional, hari di luar jadwal, sebelum
 * bergabung / setelah off-boarding, pegawai nonaktif, dan hari yang sudah punya log (termasuk izin/sakit/cuti).
 * Aman dijalankan berulang: satu log per pegawai per tanggal.
 */
class DailyAttendanceJobService
{
    public function __construct(
        private readonly WorkScheduleRepository $scheduleRepo,
        private readonly HolidayRepository $holidayRepo,
        private readonly AttendanceSettingsRepository $settingsRepo,
        private readonly AttendanceRecapRepository $recapRepo,
    ) {}

    /** @return array{created: int, skipped_no_branch: int} */
    public function autoPresent(Carbon $date, ?Carbon $now = null): array
    {
        return $this->run($date, $now ?? now(), autoMode: true);
    }

    /** @return array{created: int, skipped_no_branch: int} */
    public function markAbsent(Carbon $date, ?Carbon $now = null): array
    {
        return $this->run($date, $now ?? now(), autoMode: false);
    }

    /** @return array{created: int, skipped_no_branch: int} */
    private function run(Carbon $date, Carbon $now, bool $autoMode): array
    {
        $result = ['created' => 0, 'skipped_no_branch' => 0];
        $day = $date->copy()->startOfDay();
        $dateString = $day->toDateString();

        // Tanggal yang belum tiba tidak diproses; libur nasional bukan hari kerja.
        if ($day->gt($now->copy()->startOfDay()) || $this->holidayRepo->isHoliday($dateString)) {
            return $result;
        }

        $hasLog = EmployeeWorkAttendanceLog::whereDate('attendance_date', $dateString)->pluck('employee_id')->flip();

        Employee::active()->with('currentStatus.position')->chunkById(200, function ($employees) use ($day, $dateString, $now, $autoMode, $hasLog, &$result) {
            foreach ($employees as $employee) {
                if ($hasLog->has($employee->id) || ! $this->recapRepo->employmentWindow($employee, $day, $day)) {
                    continue;
                }

                $rule = $this->scheduleRepo->resolveForPeriod($employee, $dateString, $dateString)[$dateString] ?? null;

                if (! $rule || $this->settingsRepo->requiresManualAttendance($employee, $rule) === $autoMode) {
                    continue;
                }

                if (! $this->timeHasCome($rule, $day, $now, $autoMode)) {
                    continue;
                }

                if (! $employee->branch_id) {
                    $result['skipped_no_branch']++;   // log absensi mewajibkan cabang

                    continue;
                }

                if ($this->createLog($employee, $rule, $day, $autoMode)) {
                    $result['created']++;
                }
            }
        });

        return $result;
    }

    /** Auto-hadir mulai dari jam mulai jadwal; alpha baru setelah jam selesai jadwal (tanggal lampau selalu boleh). */
    private function timeHasCome(WorkScheduleRule $rule, Carbon $day, Carbon $now, bool $autoMode): bool
    {
        if ($day->lt($now->copy()->startOfDay())) {
            return true;
        }

        $time = $autoMode ? $rule->start_time : $rule->end_time;

        // Jadwal tanpa jam: auto-hadir langsung berlaku, alpha menunggu hari berganti
        if (! $time) {
            return $autoMode;
        }

        return $now->gte(Carbon::parse($day->toDateString().' '.$time));
    }

    private function createLog(Employee $employee, WorkScheduleRule $rule, Carbon $day, bool $autoMode): bool
    {
        $attributes = $autoMode
            ? [
                'status' => 'present',
                'check_in' => Carbon::parse($day->toDateString().' '.($rule->start_time ?: '00:00')),
                'notes' => 'Hadir otomatis (tidak wajib absen).',
            ]
            : [
                'status' => 'absent',
                'notes' => 'Alpha otomatis (tidak ada absensi).',
            ];

        try {
            $log = EmployeeWorkAttendanceLog::firstOrCreate(
                ['employee_id' => $employee->id, 'attendance_date' => $day->toDateString()],
                $attributes + ['branch_id' => $employee->branch_id, 'late_minutes' => 0, 'early_leave_minutes' => 0, 'is_location_anomaly' => false],
            );
        } catch (QueryException $e) {
            // Balapan dengan check-in manual pada detik yang sama: log sudah ada, jangan ditimpa
            if ($e->getCode() === '23000') {
                return false;
            }

            throw $e;
        }

        return $log->wasRecentlyCreated;
    }
}
