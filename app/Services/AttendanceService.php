<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\WorkScheduleRule;
use App\Repositories\AttendanceSettingsRepository;
use App\Repositories\HolidayRepository;
use App\Repositories\WorkAttendanceRepository;
use App\Repositories\WorkScheduleRepository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function __construct(
        private readonly WorkAttendanceRepository $attendanceRepo,
        private readonly WorkScheduleRepository $scheduleRepo,
        private readonly HolidayRepository $holidayRepo,
        private readonly TelegramStorageService $telegramStorage,
        private readonly AttendanceSettingsRepository $settingsRepo,
    ) {}

    public function checkIn(Employee $employee, array $data): EmployeeWorkAttendanceLog
    {
        $today = now()->toDateString();
        $settings = $this->settingsRepo->resolve($employee);

        if ($this->holidayRepo->isHoliday($today)) {
            throw ValidationException::withMessages(['date' => 'Hari ini adalah hari libur.']);
        }

        $schedule = $this->scheduleRepo->resolveForEmployee($employee->id, $today);

        // Auto-present: no manual check-in needed (setting ATAU aturan jadwal tidak mewajibkan absen)
        if (! $this->settingsRepo->requiresManualAttendance($employee, $schedule)) {
            if (! $employee->branch_id) {
                throw ValidationException::withMessages(['employee' => 'Pegawai belum memiliki cabang. Hubungi HR.']);
            }

            // Job auto-hadir mungkin sudah mencatat hari ini; jangan ditimpa
            $already = $this->attendanceRepo->findForDate($employee->id, $today);
            if ($already?->status === 'present') {
                return $already;
            }

            return $this->attendanceRepo->checkIn($employee->id, $employee->branch_id, [
                'status' => 'present',
                'late_minutes' => 0,
                'early_leave_minutes' => 0,
            ]);
        }

        if (! $schedule) {
            $dow = now()->dayOfWeekIso;
            $msg = $dow >= 6
                ? 'Hari ini bukan hari kerja (weekend).'
                : 'Tidak ada jadwal kerja aktif untuk hari ini. Hubungi HR.';
            throw ValidationException::withMessages(['schedule' => $msg]);
        }

        if (! $employee->branch_id) {
            throw ValidationException::withMessages(['employee' => 'Pegawai belum memiliki cabang. Hubungi HR.']);
        }

        $existing = $this->attendanceRepo->findForDate($employee->id, $today);

        // Hari ini sudah ditetapkan izin/sakit/cuti (pengajuan disetujui): jangan ditimpa jadi hadir.
        $leaveLabels = ['sick' => 'sakit', 'permission' => 'izin', 'leave' => 'cuti'];
        if ($existing && isset($leaveLabels[$existing->status])) {
            throw ValidationException::withMessages([
                'check_in' => "Hari ini Anda tercatat {$leaveLabels[$existing->status]}. Check-in tidak diperlukan.",
            ]);
        }

        if ($existing?->isCheckedIn()) {
            throw ValidationException::withMessages(['check_in' => 'Anda sudah melakukan check-in hari ini.']);
        }

        // Early check-in window guard
        if ($settings->earlyCheckinMinutes !== null) {
            $earliest = Carbon::parse("{$today} {$schedule->start_time}")->subMinutes($settings->earlyCheckinMinutes);
            if (now()->lt($earliest)) {
                $diff = (int) now()->diffInMinutes($earliest);
                throw ValidationException::withMessages([
                    'check_in' => "Belum waktunya check-in. {$diff} menit lagi (boleh masuk mulai ".$earliest->format('H:i').').',
                ]);
            }
        }

        $lateMinutes = $this->calculateLateMinutes(now(), $schedule, $today);

        [$distanceM, $isAnomaly] = $this->resolveLocation(
            $employee, $settings->attendanceScope,
            (float) $data['check_in_latitude'],
            (float) $data['check_in_longitude'],
        );

        if ($isAnomaly) {
            throw ValidationException::withMessages([
                'check_in_latitude' => "Anda berada di luar area absensi ({$distanceM} m dari lokasi kantor). Check-in ditolak.",
            ]);
        }

        $selfiePath = null;
        if (isset($data['selfie'])) {
            $selfiePath = $this->telegramStorage->uploadPhoto(
                $data['selfie'],
                "Check-in {$employee->full_name} ".now()->toDateTimeString(),
            );
        }

        return $this->attendanceRepo->checkIn($employee->id, $employee->branch_id, [
            'check_in_latitude' => $data['check_in_latitude'],
            'check_in_longitude' => $data['check_in_longitude'],
            'check_in_selfie_telegram_file_id' => $selfiePath,
            'check_in_selfie_path' => $selfiePath,
            'check_in_distance_m' => $distanceM,
            'check_in_notes' => $data['check_in_notes'] ?? null,
            'late_minutes' => $lateMinutes,
            'is_location_anomaly' => false,
            'anomaly_notes' => null,
        ]);
    }

    public function checkOut(Employee $employee, array $data): EmployeeWorkAttendanceLog
    {
        $today = now()->toDateString();
        $log = $this->attendanceRepo->findForDate($employee->id, $today);

        if (! $log || ! $log->isCheckedIn()) {
            throw ValidationException::withMessages(['check_out' => 'Belum melakukan check-in hari ini.']);
        }

        $schedule = $this->scheduleRepo->resolveForEmployee($employee->id, $today);
        $settings = $this->settingsRepo->resolve($employee);

        $earlyLeaveMinutes = $schedule
            ? $this->calculateEarlyLeaveMinutes(now(), $schedule, $today, $settings->earlyCheckoutMinutes)
            : 0;

        if ($earlyLeaveMinutes > 0) {
            throw ValidationException::withMessages([
                'check_out' => "Belum waktunya check-out. {$earlyLeaveMinutes} menit lagi hingga jadwal selesai.",
            ]);
        }

        $selfiePath = null;
        if (isset($data['selfie'])) {
            $selfiePath = $this->telegramStorage->uploadPhoto(
                $data['selfie'],
                "Check-out {$employee->full_name} ".now()->toDateTimeString(),
            );
        }

        // Check-out boleh dari mana saja: lokasi opsional dan hanya dicatat, tidak pernah memblokir.
        $distanceM = isset($data['check_out_latitude'], $data['check_out_longitude'])
            ? $this->resolveLocation(
                $employee, $settings->attendanceScope,
                (float) $data['check_out_latitude'],
                (float) $data['check_out_longitude'],
            )[0]
            : null;

        return $this->attendanceRepo->checkOut($log, [
            'check_out_latitude' => $data['check_out_latitude'] ?? null,
            'check_out_longitude' => $data['check_out_longitude'] ?? null,
            'check_out_selfie_telegram_file_id' => $selfiePath,
            'check_out_selfie_path' => $selfiePath,
            'check_out_distance_m' => $distanceM,
            'check_out_notes' => $data['check_out_notes'] ?? null,
            'early_leave_minutes' => $earlyLeaveMinutes,
        ]);
    }

    /**
     * Returns [distance_meters, is_anomaly].
     * Scope 'anywhere' always returns [null, false].
     * Scope 'area'   finds closest branch in employee's area.
     * Scope 'branch' uses employee's own branch.
     */
    private function resolveLocation(Employee $employee, string $scope, float $lat, float $lng): array
    {
        if ($scope === 'anywhere') {
            return [null, false];
        }

        if ($scope === 'area' && $employee->area_id) {
            $branches = Branch::where('areas_id', $employee->area_id)
                ->where('is_hq', false)
                ->whereNotNull('latitude')->whereNotNull('longitude')
                ->get();

            $closest = null;
            $minDist = PHP_INT_MAX;
            foreach ($branches as $branch) {
                $d = $this->haversineMeters($lat, $lng, (float) $branch->latitude, (float) $branch->longitude);
                if ($d < $minDist) {
                    $minDist = $d;
                    $closest = $branch;
                }
            }

            if (! $closest) {
                return [null, false];
            }

            $isAnomaly = $minDist > ($closest->radius_meters ?? PHP_INT_MAX);

            return [$minDist, $isAnomaly];
        }

        // Default: branch scope
        if (! $employee->branch || $employee->branch->is_hq) {
            return [null, false];
        }

        $distanceM = $this->haversineMeters(
            $lat, $lng,
            (float) $employee->branch->latitude,
            (float) $employee->branch->longitude,
        );

        return [$distanceM, $distanceM > ($employee->branch->radius_meters ?? PHP_INT_MAX)];
    }

    private function calculateLateMinutes(Carbon $checkIn, WorkScheduleRule $schedule, string $date): int
    {
        $deadline = Carbon::parse("{$date} {$schedule->start_time}")
            ->addMinutes($schedule->late_tolerance_minutes ?? 0);

        return $checkIn->lte($deadline) ? 0 : (int) $deadline->diffInMinutes($checkIn);
    }

    private function calculateEarlyLeaveMinutes(
        Carbon $checkOut,
        WorkScheduleRule $schedule,
        string $date,
        ?int $earlyCheckoutMinutes,
    ): int {
        $scheduledEnd = Carbon::parse("{$date} {$schedule->end_time}");
        // earlyCheckoutMinutes = how many minutes before end_time is allowed
        $tolerance = $earlyCheckoutMinutes ?? ($schedule->early_leave_tolerance_minutes ?? 0);
        $threshold = $scheduledEnd->copy()->subMinutes($tolerance);

        return $checkOut->gte($threshold) ? 0 : (int) $checkOut->diffInMinutes($threshold);
    }

    private function haversineMeters(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return (int) round($r * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
