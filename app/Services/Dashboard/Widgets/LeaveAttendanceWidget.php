<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeLeaveRequest;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class LeaveAttendanceWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'leave_attendance';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $recapQuery = EmployeeAttendanceMonthlyRecap::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)));

        $avgAttendanceRate = 0;
        $totalScheduled = (clone $recapQuery)->sum('total_scheduled_working_days');
        $totalPresent = (clone $recapQuery)->sum('total_present');

        if ($totalScheduled > 0) {
            $avgAttendanceRate = round(($totalPresent / $totalScheduled) * 100, 1);
        }

        $totalLate = (clone $recapQuery)->sum('total_late');
        $totalSick = (clone $recapQuery)->sum('total_sick');
        $totalPermission = (clone $recapQuery)->sum('total_permission');
        $totalLeave = (clone $recapQuery)->sum('total_leave');

        $leaveQuery = EmployeeLeaveRequest::whereMonth('start_date', $currentMonth)
            ->whereYear('start_date', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $totalLeaveRequests = (clone $leaveQuery)->count();

        $byLeaveStatus = (clone $leaveQuery)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $byLeaveType = (clone $leaveQuery)
            ->selectRaw('leave_type, COUNT(*) as count')
            ->groupBy('leave_type')
            ->pluck('count', 'leave_type')
            ->toArray();

        $pendingLeaves = EmployeeLeaveRequest::where('status', 'pending')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        return [
            'attendance_rate' => $avgAttendanceRate,
            'total_late' => $totalLate,
            'total_sick' => $totalSick,
            'total_permission' => $totalPermission,
            'total_leave_days' => $totalLeave,
            'total_leave_requests' => $totalLeaveRequests,
            'by_leave_status' => $byLeaveStatus,
            'by_leave_type' => $byLeaveType,
            'pending_leaves' => $pendingLeaves,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $recaps = EmployeeAttendanceMonthlyRecap::where('period_year', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->with('employee')
            ->get();

        return $recaps->map(fn ($r) => [
            'employee' => $r->employee?->full_name ?? '-',
            'period' => $r->period_month.'/'.$r->period_year,
            'scheduled_days' => $r->total_scheduled_working_days,
            'present' => $r->total_present,
            'absent' => $r->total_absent,
            'late' => $r->total_late,
            'sick' => $r->total_sick,
            'permission' => $r->total_permission,
            'leave' => $r->total_leave,
        ])->toArray();
    }
}
