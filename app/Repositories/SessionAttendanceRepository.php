<?php

namespace App\Repositories;

use App\Models\EmployeeSessionAttendanceLog;
use App\Models\SessionSchedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class SessionAttendanceRepository
{
    public function findSessionForToday(int $employeeId, int $sessionScheduleId): ?SessionSchedule
    {
        return SessionSchedule::where('employee_id', $employeeId)
            ->where('id', $sessionScheduleId)
            ->whereHas('classSchedule', fn ($q) => $q->whereDate('schedule_date', today()))
            ->with(['classSchedule.classRoom.branch'])
            ->first();
    }

    public function todaySessionsForEmployee(int $employeeId): Collection
    {
        return SessionSchedule::where('employee_id', $employeeId)
            ->whereHas('classSchedule', fn ($q) => $q->whereDate('schedule_date', today()))
            ->with(['classSchedule.classRoom', 'attendanceLog'])
            ->get();
    }

    public function findLog(int $sessionScheduleId, int $employeeId): ?EmployeeSessionAttendanceLog
    {
        return EmployeeSessionAttendanceLog::where('session_schedule_id', $sessionScheduleId)
            ->where('employee_id', $employeeId)
            ->with(['sessionSchedule.classSchedule.classRoom.branch'])
            ->first();
    }

    public function checkIn(SessionSchedule $session, array $data): EmployeeSessionAttendanceLog
    {
        $log = EmployeeSessionAttendanceLog::firstOrCreate(
            ['session_schedule_id' => $session->id, 'employee_id' => $session->employee_id],
            ['status' => 'absent', 'late_minutes' => 0],
        );

        $log->update(array_merge($data, [
            'check_in' => now(),
            'status' => 'checked_in',
        ]));

        return $log;
    }

    public function checkOut(EmployeeSessionAttendanceLog $log, array $data): EmployeeSessionAttendanceLog
    {
        $log->update(array_merge($data, [
            'check_out' => now(),
            'status' => 'present',
        ]));

        return $log;
    }

    public function adjust(EmployeeSessionAttendanceLog $log, array $data, int $adjustedByEmployeeId): EmployeeSessionAttendanceLog
    {
        $log->adjustments()->create([
            'adjusted_by_employee_id' => $adjustedByEmployeeId,
            'previous_status' => $log->status,
            'previous_check_in' => $log->check_in,
            'previous_check_out' => $log->check_out,
            'new_status' => $data['status'] ?? $log->status,
            'new_check_in' => $data['check_in'] ?? $log->check_in,
            'new_check_out' => $data['check_out'] ?? $log->check_out,
            'adjustment_reason' => $data['adjustment_reason'],
        ]);

        $log->update($data);

        return $log;
    }

    public function verify(EmployeeSessionAttendanceLog $log, int $verifierEmployeeId): EmployeeSessionAttendanceLog
    {
        if ($log->verified_at !== null) {
            throw ValidationException::withMessages([
                'session_log' => 'Log absensi sesi ini sudah diverifikasi sebelumnya.',
            ]);
        }

        $log->update([
            'verified_by_employee_id' => $verifierEmployeeId,
            'verified_at' => now(),
        ]);

        return $log;
    }
}
