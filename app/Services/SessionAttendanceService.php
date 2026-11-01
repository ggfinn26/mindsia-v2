<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeSessionAttendanceLog;
use App\Models\SessionSchedule;
use App\Repositories\SessionAttendanceRepository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class SessionAttendanceService
{
    /** Menit sebelum jam mulai sesi saat check-in mulai diizinkan. */
    public const EARLY_CHECK_IN_MINUTES = 5;

    public function __construct(
        private readonly SessionAttendanceRepository $repo,
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function checkIn(Employee $employee, int $sessionScheduleId, array $data): EmployeeSessionAttendanceLog
    {
        $session = $this->repo->findSessionForToday($employee->id, $sessionScheduleId);

        abort_unless($session, 404, 'Sesi tidak ditemukan untuk hari ini.');

        $existing = $this->repo->findLog($session->id, $employee->id);

        if ($existing?->isCheckedIn()) {
            throw ValidationException::withMessages(['check_in' => 'Anda sudah check-in untuk sesi ini.']);
        }

        // Check-in sesi baru dibuka EARLY_CHECK_IN_MINUTES sebelum jam mulai; lebih awal dari itu ditolak.
        $scheduledStart = Carbon::parse($session->classSchedule->schedule_date->toDateString().' '.$session->classSchedule->start_time);
        $opensAt = $scheduledStart->copy()->subMinutes(self::EARLY_CHECK_IN_MINUTES);

        if (now()->lt($opensAt)) {
            throw ValidationException::withMessages([
                'check_in' => 'Belum waktunya check-in. Sesi mulai pukul '.$scheduledStart->format('H:i').', check-in dibuka pukul '.$opensAt->format('H:i').' ('.self::EARLY_CHECK_IN_MINUTES.' menit sebelum mulai).',
            ]);
        }

        $lateMinutes = $this->calculateLateMinutes(now(), $session);
        $branch = $session->classSchedule->classRoom->branch;
        $distanceM = $branch
            ? $this->haversineMeters(
                $data['check_in_latitude'],
                $data['check_in_longitude'],
                $branch->latitude,
                $branch->longitude,
            )
            : null;
        $isAnomaly = $branch && $distanceM !== null
            ? $distanceM > $branch->radius_meters
            : false;

        $selfiePath = null;
        if (isset($data['selfie'])) {
            $uploaded = $this->telegramStorage->uploadFile(
                $data['selfie']->getRealPath(),
                $data['selfie']->getClientOriginalName(),
                'session_checkin',
                $employee->id,
            );
            $selfiePath = $uploaded['file_id'];
        }

        return $this->repo->checkIn($session, [
            'check_in_latitude' => $data['check_in_latitude'],
            'check_in_longitude' => $data['check_in_longitude'],
            'check_in_selfie_telegram_file_id' => $selfiePath,
            'check_in_selfie_path' => $selfiePath,
            'check_in_distance_m' => $distanceM,
            'check_in_notes' => $data['check_in_notes'] ?? null,
            'late_minutes' => $lateMinutes,
            'is_location_anomaly' => $isAnomaly,
            'anomaly_notes' => $isAnomaly ? "Jarak {$distanceM}m dari kantor" : null,
        ]);
    }

    public function checkOut(Employee $employee, int $sessionScheduleId, array $data): EmployeeSessionAttendanceLog
    {
        $log = $this->repo->findLog($sessionScheduleId, $employee->id);

        abort_unless($log, 404);

        if (! $log->isCheckedIn()) {
            throw ValidationException::withMessages(['check_out' => 'Belum check-in untuk sesi ini.']);
        }

        // Batas absen keluar 23:59 pada hari sesi; setelah itu sesi otomatis dicatat hadir tanpa absen keluar.
        if (! $log->sessionSchedule?->classSchedule?->schedule_date?->isToday()) {
            throw ValidationException::withMessages(['check_out' => 'Batas absen keluar (23:59) untuk sesi ini sudah lewat.']);
        }

        $selfiePath = null;
        if (isset($data['selfie'])) {
            $uploaded = $this->telegramStorage->uploadFile(
                $data['selfie']->getRealPath(),
                $data['selfie']->getClientOriginalName(),
                'session_checkout',
                $employee->id,
            );
            $selfiePath = $uploaded['file_id'];
        }

        // Check-out boleh dari mana saja: lokasi opsional, hanya dicatat bila ada.
        $latitude = $data['check_out_latitude'] ?? null;
        $longitude = $data['check_out_longitude'] ?? null;
        $branch = $log->sessionSchedule?->classSchedule?->classRoom?->branch;

        return $this->repo->checkOut($log, [
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'check_out_selfie_telegram_file_id' => $selfiePath,
            'check_out_selfie_path' => $selfiePath,
            'check_out_distance_m' => $latitude !== null && $longitude !== null && $branch
                ? $this->haversineMeters((float) $latitude, (float) $longitude, (float) $branch->latitude, (float) $branch->longitude)
                : null,
            'check_out_notes' => $data['check_out_notes'] ?? null,
        ]);
    }

    private function haversineMeters(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return (int) round($r * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    private function calculateLateMinutes(Carbon $checkIn, SessionSchedule $session): int
    {
        $scheduledStart = Carbon::parse(
            $session->classSchedule->schedule_date->toDateString()
            .' '.$session->classSchedule->start_time
        );
        $deadline = $scheduledStart->copy()->addMinutes($session->classSchedule->late_tolerance_minutes ?? 0);

        if ($checkIn->lte($deadline)) {
            return 0;
        }

        return (int) $deadline->diffInMinutes($checkIn);
    }
}
