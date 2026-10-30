<?php

namespace App\Services\Attendance;

use App\Models\Employee;
use App\Models\EmployeeSessionAttendanceLog;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\SessionSchedule;
use App\Repositories\HolidayRepository;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Absensi sesi tutor per HARI, dipakai bersama oleh payroll (potongan alpha sesi) dan evaluator aturan absensi.
 */
class SessionAbsenceService
{
    public function __construct(private readonly HolidayRepository $holidayRepo) {}

    /**
     * Tanggal yang punya sesi terjadwal, dan di antaranya tanggal alpha: tidak satu pun sesi dihadiri.
     * Dikecualikan dari alpha: tanggal yang belum lewat, libur nasional, hari yang tercatat sakit/izin/cuti/libur di log
     * kerja, dan (bila $excludeWorkAbsent) hari yang sudah dihitung sebagai alpha kerja agar tidak dihitung dua kali.
     * Sesi berstatus checked_in dianggap hadir (tutor sudah datang walau belum difinalkan job lupa-absen-keluar).
     *
     * @return array{dates: Collection<int, string>, absent: Collection<int, string>}
     */
    public function analyze(Employee $employee, CarbonInterface $start, CarbonInterface $end, bool $excludeWorkAbsent, ?CarbonInterface $now = null): array
    {
        $from = $start->toDateString();
        $to = $end->toDateString();
        $today = ($now ?? now())->toDateString();

        $sessions = SessionSchedule::where('employee_id', $employee->id)
            ->whereHas('classSchedule', fn ($q) => $q->whereBetween('schedule_date', [$from, $to]))
            ->with('classSchedule:id,schedule_date')
            ->get(['id', 'class_schedule_id']);

        if ($sessions->isEmpty()) {
            return ['dates' => collect(), 'absent' => collect()];
        }

        $attended = EmployeeSessionAttendanceLog::where('employee_id', $employee->id)
            ->whereIn('status', ['present', 'checked_in'])
            ->whereIn('session_schedule_id', $sessions->pluck('id'))
            ->pluck('session_schedule_id')->flip();

        $excused = ['sick', 'permission', 'leave', 'holiday'];
        if ($excludeWorkAbsent) {
            $excused[] = 'absent';
        }

        $workStatus = EmployeeWorkAttendanceLog::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$from, $to])
            ->get(['status', 'attendance_date'])
            ->mapWithKeys(fn ($log) => [$log->attendance_date->toDateString() => $log->status]);

        $holidays = array_flip($this->holidayRepo->datesBetween($from, $to));

        $byDate = $sessions->groupBy(fn ($session) => $session->classSchedule->schedule_date->toDateString())->sortKeys();

        $absent = $byDate
            ->filter(fn ($daySessions, $date) => $date < $today
                && ! isset($holidays[$date])
                && ! in_array($workStatus[$date] ?? null, $excused, true)
                && $daySessions->every(fn ($session) => ! $attended->has($session->id)))
            ->keys()->values();

        return ['dates' => $byDate->keys()->values(), 'absent' => $absent];
    }

    /**
     * Keterlambatan sesi per tanggal (menit terlambat terbesar pada hari itu), hanya sesi yang dihadiri dan terlambat.
     *
     * @return Collection<string, int> tanggal => menit
     */
    public function lateMinutesByDate(Employee $employee, CarbonInterface $start, CarbonInterface $end): Collection
    {
        return EmployeeSessionAttendanceLog::where('employee_id', $employee->id)
            ->whereIn('status', ['present', 'checked_in'])
            ->where('late_minutes', '>', 0)
            ->whereHas('sessionSchedule.classSchedule', fn ($q) => $q->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]))
            ->with('sessionSchedule.classSchedule:id,schedule_date')
            ->get(['id', 'session_schedule_id', 'late_minutes'])
            ->groupBy(fn ($log) => $log->sessionSchedule->classSchedule->schedule_date->toDateString())
            ->map(fn ($logs) => (int) $logs->max('late_minutes'))
            ->sortKeys();
    }

    /** Jumlah sesi terlambat dan total menitnya pada rentang itu. @return array{count: int, minutes: int} */
    public function lateTotals(Employee $employee, CarbonInterface $start, CarbonInterface $end): array
    {
        $logs = EmployeeSessionAttendanceLog::where('employee_id', $employee->id)
            ->whereIn('status', ['present', 'checked_in'])
            ->where('late_minutes', '>', 0)
            ->whereHas('sessionSchedule.classSchedule', fn ($q) => $q->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]))
            ->get(['late_minutes']);

        return ['count' => $logs->count(), 'minutes' => (int) $logs->sum('late_minutes')];
    }
}
