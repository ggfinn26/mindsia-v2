<?php

namespace App\Repositories;

use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeWorkAttendanceLog;
use Illuminate\Support\Carbon;

class AttendanceRecapRepository
{
    public function upsertForEmployee(int $employeeId, Carbon $date): EmployeeAttendanceMonthlyRecap
    {
        $year = $date->year;
        $month = $date->month;

        $totals = EmployeeWorkAttendanceLog::where('employee_id', $employeeId)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->selectRaw("
                SUM(status = 'present')            AS total_present,
                SUM(status = 'checked_in')         AS total_checked_in,
                SUM(status = 'absent')             AS total_absent,
                SUM(late_minutes > 0)              AS total_late,
                COALESCE(SUM(late_minutes), 0)     AS total_late_minutes,
                SUM(early_leave_minutes > 0)       AS total_early_leave,
                SUM(status = 'sick')               AS total_sick,
                SUM(status = 'permission')         AS total_permission,
                SUM(status = 'leave')              AS total_leave,
                SUM(status = 'holiday')            AS total_holiday
            ")
            ->first();

        $present = (int) ($totals->total_present ?? 0);
        $checkedIn = (int) ($totals->total_checked_in ?? 0);

        return EmployeeAttendanceMonthlyRecap::updateOrCreate(
            ['employee_id' => $employeeId, 'period_year' => $year, 'period_month' => $month],
            [
                'total_scheduled_working_days' => 0, // ponytail: defer scheduled days calc
                'total_effective_working_days' => $present + $checkedIn,
                'total_present' => $present,
                'total_checked_in' => $checkedIn,
                'total_absent' => (int) ($totals->total_absent ?? 0),
                'total_late' => (int) ($totals->total_late ?? 0),
                'total_late_minutes' => (int) ($totals->total_late_minutes ?? 0),
                'total_early_leave' => (int) ($totals->total_early_leave ?? 0),
                'total_sick' => (int) ($totals->total_sick ?? 0),
                'total_permission' => (int) ($totals->total_permission ?? 0),
                'total_leave' => (int) ($totals->total_leave ?? 0),
                'total_holiday' => (int) ($totals->total_holiday ?? 0),
                'generated_at' => now(),
            ],
        );
    }
}
