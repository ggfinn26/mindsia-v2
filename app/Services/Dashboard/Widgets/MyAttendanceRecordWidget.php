<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeWorkAttendanceLog;
use App\Models\LeaveRequest;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MyAttendanceRecordWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_attendance_record';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $logs = EmployeeWorkAttendanceLog::where('employee_id', $employeeId)
            ->whereMonth('attendance_date', $now->month)
            ->whereYear('attendance_date', $now->year)
            ->get();

        $present = $logs->where('status', 'present')->count();
        $late = $logs->where('is_late', true)->count();
        $absent = $logs->where('status', 'absent')->count();
        $total = $logs->count();

        $pendingLeave = LeaveRequest::where('employee_id', $employeeId)
            ->where('status', 'pending')
            ->count();

        $approvedLeaveThisMonth = LeaveRequest::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereMonth('start_date', $now->month)
            ->whereYear('start_date', $now->year)
            ->count();

        return [
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'total_working_days' => $total,
            'pending_leave_requests' => $pendingLeave,
            'approved_leave_this_month' => $approvedLeaveThisMonth,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return EmployeeWorkAttendanceLog::where('employee_id', $employeeId)
            ->whereMonth('attendance_date', $now->month)
            ->whereYear('attendance_date', $now->year)
            ->orderBy('attendance_date')
            ->get()
            ->map(fn ($l) => [
                'date' => $l->attendance_date?->format('Y-m-d'),
                'status' => $l->status,
                'check_in' => $l->check_in_time,
                'check_out' => $l->check_out_time,
                'is_late' => $l->is_late ? 'Ya' : 'Tidak',
            ])->toArray();
    }
}
