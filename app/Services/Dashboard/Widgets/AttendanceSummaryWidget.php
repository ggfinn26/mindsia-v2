<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeWorkAttendanceLog;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class AttendanceSummaryWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'attendance_summary';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $recaps = EmployeeAttendanceMonthlyRecap::where('period_year', $currentYear)
            ->where('period_month', $currentMonth)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->get();

        $totalScheduled = $recaps->sum('total_scheduled_working_days');
        $totalPresent = $recaps->sum('total_present');
        $totalAbsent = $recaps->sum('total_absent');
        $totalLate = $recaps->sum('total_late');
        $totalSick = $recaps->sum('total_sick');
        $totalPermission = $recaps->sum('total_permission');
        $totalLeave = $recaps->sum('total_leave');
        $totalLateMinutes = $recaps->sum('total_late_minutes');

        $attendanceRate = $totalScheduled > 0
            ? round(($totalPresent / $totalScheduled) * 100, 1)
            : 0;

        $anomalyCount = EmployeeWorkAttendanceLog::whereMonth('attendance_date', $currentMonth)
            ->whereYear('attendance_date', $currentYear)
            ->where('is_location_anomaly', true)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $prevMonth = $now->copy()->subMonth();
        $prevRecaps = EmployeeAttendanceMonthlyRecap::where('period_year', $prevMonth->year)
            ->where('period_month', $prevMonth->month)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->get();

        $prevScheduled = $prevRecaps->sum('total_scheduled_working_days');
        $prevPresent = $prevRecaps->sum('total_present');
        $prevRate = $prevScheduled > 0 ? ($prevPresent / $prevScheduled) * 100 : 0;

        $trend = $prevRate > 0
            ? round($attendanceRate - $prevRate, 1)
            : 0;

        return [
            'attendance_rate' => $attendanceRate,
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent,
            'total_late' => $totalLate,
            'total_sick' => $totalSick,
            'total_permission' => $totalPermission,
            'total_leave' => $totalLeave,
            'total_late_minutes' => $totalLateMinutes,
            'anomaly_count' => $anomalyCount,
            'employee_count' => $recaps->count(),
            'trend_percent' => $trend,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $recaps = EmployeeAttendanceMonthlyRecap::where('period_year', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->with('employee')
            ->orderBy('period_month')
            ->get();

        return $recaps->map(fn ($r) => [
            'employee' => $r->employee?->fullname ?? '-',
            'period' => "{$r->period_year}-{$r->period_month}",
            'scheduled' => $r->total_scheduled_working_days,
            'present' => $r->total_present,
            'absent' => $r->total_absent,
            'late' => $r->total_late,
            'sick' => $r->total_sick,
            'permission' => $r->total_permission,
            'leave' => $r->total_leave,
        ])->toArray();
    }
}
