<?php

namespace App\Repositories;

use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeWorkAttendanceLog;
use Illuminate\Support\Carbon;

class AttendanceRecapRepository
{
    /**
     * Recompute and UPSERT monthly recap for an employee after any attendance mutation.
     */
    public function upsertForEmployee(int $employeeId, Carbon $date): EmployeeAttendanceMonthlyRecap
    {
        $year = $date->year;
        $month = $date->month;

        $logs = EmployeeWorkAttendanceLog::where('employee_id', $employeeId)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get();

        $totals = [
            'total_present' => $logs->where('status', 'present')->count(),
            'total_checked_in' => $logs->where('status', 'checked_in')->count(),
            'total_absent' => $logs->where('status', 'absent')->count(),
            'total_late' => $logs->where('late_minutes', '>', 0)->count(),
            'total_late_minutes' => (int) $logs->sum('late_minutes'),
            'total_early_leave' => $logs->where('early_leave_minutes', '>', 0)->count(),
            'total_sick' => $logs->where('status', 'sick')->count(),
            'total_permission' => $logs->where('status', 'permission')->count(),
            'total_leave' => $logs->where('status', 'leave')->count(),
            'total_holiday' => $logs->where('status', 'holiday')->count(),
            'generated_at' => now(),
        ];

        EmployeeAttendanceMonthlyRecap::updateOrCreate(
            ['employee_id' => $employeeId, 'period_year' => $year, 'period_month' => $month],
            array_merge($totals, [
                'total_scheduled_working_days' => 0, // ponytail: defer scheduled days calc
                'total_effective_working_days' => $totals['total_present'] + $totals['total_checked_in'],
            ]),
        );

        return EmployeeAttendanceMonthlyRecap::where('employee_id', $employeeId)
            ->forPeriod($year, $month)
            ->firstOrFail();
    }
}
