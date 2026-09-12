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

        $lateMinutes = $this->calculateLateMinutes(now(), $session);
        $distanceM = $data['check_in_distance_m'] ?? null;
        $branch = $session->classSchedule->classRoom->branch;
        $isAnomaly = $branch && $distanceM !== null
            ? $distanceM > $branch->radius_meters
            : false;

        $telegramFileId = null;
        if (isset($data['selfie'])) {
            $uploaded = $this->telegramStorage->uploadFile(
                $data['selfie']->getRealPath(),
                $data['selfie']->getClientOriginalName(),
                'session_checkin',
                $employee->id,
            );
            $telegramFileId = $uploaded['telegram_file_id'];
        }

        return $this->repo->checkIn($session, [
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

    public function checkOut(Employee $employee, int $sessionScheduleId, array $data): EmployeeSessionAttendanceLog
    {
        $log = $this->repo->findLog($sessionScheduleId, $employee->id);

        abort_unless($log, 404);

        if (! $log->isCheckedIn()) {
            throw ValidationException::withMessages(['check_out' => 'Belum check-in untuk sesi ini.']);
        }

        $telegramFileId = null;
        if (isset($data['selfie'])) {
            $uploaded = $this->telegramStorage->uploadFile(
                $data['selfie']->getRealPath(),
                $data['selfie']->getClientOriginalName(),
                'session_checkout',
                $employee->id,
            );
            $telegramFileId = $uploaded['telegram_file_id'];
        }

        return $this->repo->checkOut($log, [
            'check_out_latitude' => $data['check_out_latitude'],
            'check_out_longitude' => $data['check_out_longitude'],
            'check_out_selfie_telegram_file_id' => $telegramFileId,
            'check_out_distance_m' => $data['check_out_distance_m'] ?? null,
            'check_out_notes' => $data['check_out_notes'] ?? null,
        ]);
    }

    private function calculateLateMinutes(Carbon $checkIn, SessionSchedule $session): int
    {
        $scheduledStart = Carbon::parse(
            $session->classSchedule->schedule_date->toDateString()
            .' '.$session->classSchedule->start_time
        );

        if ($checkIn->lte($scheduledStart)) {
            return 0;
        }

        return (int) $scheduledStart->diffInMinutes($checkIn);
    }
}
