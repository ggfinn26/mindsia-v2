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

    public function getData(array $filters = []): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $todaySessions = SessionSchedule::where('employee_id', $employeeId)
            ->whereHas('classSchedule', fn ($q) => $q->whereDate('schedule_date', $now->toDateString()))
            ->with('classSchedule:id,schedule_date,start_time,end_time', 'classSchedule.class:id,program_id', 'classSchedule.class.program:id,program_name')
            ->get()
            ->map(fn ($s) => [
                'program' => $s->classSchedule?->class?->program?->program_name ?? '-',
                'start_time' => $s->classSchedule?->start_time,
                'end_time' => $s->classSchedule?->end_time,
                'schedule_date' => $s->classSchedule?->schedule_date?->format('Y-m-d'),
            ])->toArray();

        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();

        $weekCount = SessionSchedule::where('employee_id', $employeeId)
            ->whereHas('classSchedule', fn ($q) => $q->whereBetween('schedule_date', [$weekStart->toDateString(), $weekEnd->toDateString()]))
            ->count();

        $monthCount = SessionSchedule::where('employee_id', $employeeId)
            ->whereHas('classSchedule', fn ($q) => $q->whereMonth('schedule_date', $now->month)->whereYear('schedule_date', $now->year))
            ->count();

        return [
            'today_sessions' => $todaySessions,
            'today_count' => count($todaySessions),
            'week_count' => $weekCount,
            'month_count' => $monthCount,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return SessionSchedule::where('employee_id', $employeeId)
            ->whereHas('classSchedule', fn ($q) => $q->whereMonth('schedule_date', $now->month)->whereYear('schedule_date', $now->year))
            ->with('classSchedule:id,schedule_date,start_time,end_time', 'classSchedule.class.program:id,program_name')
            ->get()
            ->map(fn ($s) => [
                'schedule_date' => $s->classSchedule?->schedule_date?->format('Y-m-d'),
                'program' => $s->classSchedule?->class?->program?->program_name ?? '-',
                'start_time' => $s->classSchedule?->start_time,
                'end_time' => $s->classSchedule?->end_time,
            ])->toArray();
    }
}
