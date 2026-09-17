<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\SessionSchedule;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MySessionScheduleWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_session_schedule';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        // Today's sessions
        $todaySessions = SessionSchedule::where('tutor_employee_id', $employeeId)
            ->whereDate('session_date', $now->toDateString())
            ->with('memberClass.program:id,program_name')
            ->orderBy('start_time')
            ->get()
            ->map(fn ($s) => [
                'program' => $s->memberClass?->program?->program_name ?? '-',
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'status' => $s->status ?? 'scheduled',
            ])->toArray();

        // This week
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();
        $weekCount = SessionSchedule::where('tutor_employee_id', $employeeId)
            ->whereBetween('session_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->count();

        // This month
        $monthCount = SessionSchedule::where('tutor_employee_id', $employeeId)
            ->whereMonth('session_date', $now->month)
            ->whereYear('session_date', $now->year)
            ->count();

        return [
            'today_sessions' => $todaySessions,
            'today_count' => count($todaySessions),
            'week_count' => $weekCount,
            'month_count' => $monthCount,
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

        return SessionSchedule::where('tutor_employee_id', $employeeId)
            ->whereMonth('session_date', $now->month)
            ->whereYear('session_date', $now->year)
            ->with('memberClass.program:id,program_name')
            ->orderBy('session_date')
            ->get()
            ->map(fn ($s) => [
                'session_date' => $s->session_date?->format('Y-m-d'),
                'program' => $s->memberClass?->program?->program_name ?? '-',
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'status' => $s->status ?? '-',
            ])->toArray();
    }
}
