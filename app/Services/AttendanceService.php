<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\WorkScheduleRule;
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
    ) {}

    public function checkIn(Employee $employee, array $data): EmployeeWorkAttendanceLog
    {
        $today = now()->toDateString();

        if ($this->holidayRepo->isHoliday($today)) {
            throw ValidationException::withMessages(['date' => 'Hari ini adalah hari libur.']);
        }

        $schedule = $this->scheduleRepo->resolveForEmployee($employee->id, $today);

        // If attendance is not required, auto-mark present without physical check-in
        if ($schedule && ! $schedule->is_required) {
            return $this->attendanceRepo->checkIn($employee->id, $employee->branch_id, [
                'status' => 'present',
                'late_minutes' => 0,
                'early_leave_minutes' => 0,
            ]);
        }

        $existing = $this->attendanceRepo->findForDate($employee->id, $today);

        if ($existing?->isCheckedIn()) {
            throw ValidationException::withMessages(['check_in' => 'Anda sudah melakukan check-in hari ini.']);
        }

        $lateMinutes = $schedule
            ? $this->calculateLateMinutes(now(), $schedule, $today)
            : 0;

        $distanceM = $employee->branch
            ? $this->haversineMeters(
                $data['check_in_latitude'],
                $data['check_in_longitude'],
                $employee->branch->latitude,
                $employee->branch->longitude,
            )
            : null;
        $isAnomaly = $employee->branch && $distanceM !== null
            ? $distanceM > $employee->branch->radius_meters
            : false;

        $telegramFileId = null;
        if (isset($data['selfie'])) {
            $uploaded = $this->telegramStorage->uploadFile(
                $data['selfie']->getRealPath(),
                $data['selfie']->getClientOriginalName(),
                'attendance_checkin',
                $employee->id,
            );
            $telegramFileId = $uploaded['telegram_file_id'];
        }

        return $this->attendanceRepo->checkIn($employee->id, $employee->branch_id, [
            'check_in_latitude' => $data['check_in_latitude'],
            'check_in_longitude' => $data['check_in_longitude'],
            'check_in_selfie_telegram_file_id' => $telegramFileId,
            'check_in_distance_m' => $distanceM,
            'check_in_notes' => $data['check_in_notes'] ?? null,
            'late_minutes' => $lateMinutes,
            'is_location_anomaly' => $isAnomaly,
            'anomaly_notes' => $isAnomaly ? "Jarak {$distanceM}m dari kantor" : null,
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

        $earlyLeaveMinutes = $schedule
            ? $this->calculateEarlyLeaveMinutes(now(), $schedule, $today)
            : 0;

        if ($earlyLeaveMinutes > 0) {
            throw ValidationException::withMessages([
                'check_out' => "Belum waktunya check-out. {$earlyLeaveMinutes} menit lagi hingga jadwal selesai.",
            ]);
        }

        $telegramFileId = null;
        if (isset($data['selfie'])) {
            $uploaded = $this->telegramStorage->uploadFile(
                $data['selfie']->getRealPath(),
                $data['selfie']->getClientOriginalName(),
                'attendance_checkout',
                $employee->id,
            );
            $telegramFileId = $uploaded['telegram_file_id'];
        }

        return $this->attendanceRepo->checkOut($log, [
            'check_out_latitude' => $data['check_out_latitude'],
            'check_out_longitude' => $data['check_out_longitude'],
            'check_out_selfie_telegram_file_id' => $telegramFileId,
            'check_out_distance_m' => $employee->branch
                ? $this->haversineMeters(
                    $data['check_out_latitude'],
                    $data['check_out_longitude'],
                    $employee->branch->latitude,
                    $employee->branch->longitude,
                )
                : null,
            'check_out_notes' => $data['check_out_notes'] ?? null,
            'early_leave_minutes' => $earlyLeaveMinutes,
        ]);
    }

    private function calculateLateMinutes(Carbon $checkIn, WorkScheduleRule $schedule, string $date): int
    {
        $scheduledStart = Carbon::parse("{$date} {$schedule->start_time}");
        $deadline = $scheduledStart->copy()->addMinutes($schedule->late_tolerance_minutes ?? 0);

        if ($checkIn->lte($deadline)) {
            return 0;
        }

        return (int) $scheduledStart->diffInMinutes($checkIn);
    }

    private function calculateEarlyLeaveMinutes(Carbon $checkOut, WorkScheduleRule $schedule, string $date): int
    {
        $scheduledEnd = Carbon::parse("{$date} {$schedule->end_time}");
        $threshold = $scheduledEnd->copy()->subMinutes($schedule->early_leave_tolerance_minutes ?? 0);

        if ($checkOut->gte($threshold)) {
            return 0;
        }

        return (int) $checkOut->diffInMinutes($scheduledEnd);
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
