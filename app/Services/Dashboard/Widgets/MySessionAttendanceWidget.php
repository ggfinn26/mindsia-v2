<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeSessionAttendanceLog;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MySessionAttendanceWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_session_attendance';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $logs = EmployeeSessionAttendanceLog::where('employee_id', $employeeId)
            ->whereMonth('session_date', $now->month)
            ->whereYear('session_date', $now->year)
            ->get();

        $attended = $logs->where('status', 'present')->count();
        $absent = $logs->where('status', 'absent')->count();
        $total = $logs->count();
        $attendanceRate = $total > 0
            ? round(($attended / $total) * 100, 1)
            : 0;

        return [
            'attended' => $attended,
            'absent' => $absent,
            'total' => $total,
            'attendance_rate' => $attendanceRate,
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

        return EmployeeSessionAttendanceLog::where('employee_id', $employeeId)
            ->whereMonth('session_date', $now->month)
            ->whereYear('session_date', $now->year)
            ->orderBy('session_date')
            ->get()
            ->map(fn ($l) => [
                'session_date' => $l->session_date?->format('Y-m-d'),
                'status' => $l->status,
                'note' => $l->note ?? '-',
            ])->toArray();
    }
}
