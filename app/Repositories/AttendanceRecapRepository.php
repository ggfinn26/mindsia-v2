<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeWorkAttendanceLog;
use Illuminate\Support\Carbon;

class AttendanceRecapRepository
{
    public function __construct(
        private readonly WorkScheduleRepository $scheduleRepo,
        private readonly HolidayRepository $holidayRepo,
    ) {}

    public function upsertForEmployee(int $employeeId, Carbon $date): EmployeeAttendanceMonthlyRecap
    {
        $year = $date->year;
        $month = $date->month;
        $employee = Employee::findOrFail($employeeId);
        $first = Carbon::create($year, $month, 1)->startOfDay();
        $last = $first->copy()->endOfMonth()->startOfDay();

        $holidays = array_flip($this->holidayRepo->datesBetween($first->toDateString(), $last->toDateString()));
        $logs = EmployeeWorkAttendanceLog::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$first->toDateString(), $last->toDateString()])
            ->get(['status', 'late_minutes', 'early_leave_minutes', 'attendance_date']);

        $count = fn (array $statuses, bool $excludeHolidays = false) => $logs
            ->filter(fn ($log) => in_array($log->status, $statuses, true)
                && ! ($excludeHolidays && isset($holidays[$log->attendance_date->toDateString()])))
            ->count();

        $present = $count(['present', 'present_late']);
        $checkedIn = $count(['checked_in', 'late']);
        $calendar = $this->workCalendar($employee, $year, $month);

        // Izin/sakit/cuti/alpha yang jatuh di hari yang (belakangan) ditetapkan libur tidak dihitung:
        // hari libur bukan hari kerja, jadi tidak boleh masuk rekap maupun potongan payroll.
        return EmployeeAttendanceMonthlyRecap::updateOrCreate(
            ['employee_id' => $employeeId, 'period_year' => $year, 'period_month' => $month],
            [
                'total_scheduled_working_days' => $calendar['scheduled'],
                'total_effective_working_days' => $present + $checkedIn,
                'total_present' => $present,
                'total_checked_in' => $checkedIn,
                'total_absent' => $count(['absent'], true),
                'total_late' => $logs->filter(fn ($log) => $log->late_minutes > 0)->count(),
                'total_late_minutes' => (int) $logs->sum('late_minutes'),
                'total_early_leave' => $logs->filter(fn ($log) => $log->early_leave_minutes > 0)->count(),
                'total_sick' => $count(['sick'], true),
                'total_permission' => $count(['permission'], true),
                'total_leave' => $count(['leave'], true),
                'total_holiday' => $calendar['holidays'],
                'generated_at' => now(),
            ],
        );
    }

    /**
     * Hari kerja yang benar-benar dijadwalkan untuk pegawai di bulan itu: hari kerja menurut jadwal efektifnya
     * (pegawai > posisi > role), bukan libur nasional, mulai tanggal bergabung, berhenti setelah off-boarding.
     * Pegawai tanpa jadwal, atau yang tidak wajib absen (setting maupun jadwal), tidak punya hari terjadwal (0).
     */
    public function scheduledWorkingDays(Employee $employee, int $year, int $month): int
    {
        return $this->workCalendar($employee, $year, $month)['scheduled'];
    }

    /**
     * Kalender kerja pegawai di satu bulan.
     * scheduled = hari kerja menurut jadwal efektif, bukan libur; holidays = libur nasional yang jatuh di hari kerja jadwalnya.
     *
     * @return array{scheduled: int, holidays: int}
     */
    public function workCalendar(Employee $employee, int $year, int $month): array
    {
        $first = Carbon::create($year, $month, 1)->startOfDay();
        $window = $this->employmentWindow($employee, $first, $first->copy()->endOfMonth()->startOfDay());

        if (! $window) {
            return ['scheduled' => 0, 'holidays' => 0];
        }

        [$start, $end] = $window;
        $holidays = array_flip($this->holidayRepo->datesBetween($start->toDateString(), $end->toDateString()));
        $days = collect($this->scheduleRepo->resolveForPeriod($employee, $start->toDateString(), $end->toDateString()));

        // Pegawai tidak wajib absen tetap punya hari terjadwal: job auto-hadir mencatat mereka hadir di hari-hari itu.
        return [
            'scheduled' => $days->filter(fn ($rule, $date) => ! isset($holidays[$date]))->count(),
            'holidays' => $days->filter(fn ($rule, $date) => isset($holidays[$date]))->count(),
        ];
    }

    /**
     * Jendela masa kerja pegawai di dalam [from, to]: mulai dari tanggal bergabung, berhenti setelah off-boarding
     * (hanya bila tidak punya status aktif). Null bila tidak ada hari tersisa.
     *
     * @return array{0: Carbon, 1: Carbon}|null
     */
    public function employmentWindow(Employee $employee, Carbon $from, Carbon $to): ?array
    {
        $start = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();

        if ($employee->join_date && $employee->join_date->gt($start)) {
            $start = $employee->join_date->copy()->startOfDay();
        }

        $employee->loadMissing('currentStatus.position');

        if (! $employee->currentStatus) {
            $offBoarded = $employee->employmentStatuses()->with('offBoarding')->get()
                ->map(fn ($status) => $status->offBoarding?->off_boarding_date)
                ->filter()
                ->max();

            if ($offBoarded && Carbon::parse($offBoarded)->lt($end)) {
                $end = Carbon::parse($offBoarded)->startOfDay();
            }
        }

        return $start->gt($end) ? null : [$start, $end];
    }
}
