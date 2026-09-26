<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\SocializationSchedule;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class SocializationScheduleWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'socialization_schedule';
    }

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();

        $upcoming = SocializationSchedule::where('schedule_date', '>=', $now->toDateString())
            ->when($branchId, fn ($q) => $q->whereHas('socialization', fn ($s) => $s->where('branch_id', $branchId)))
            ->with('socialization:id,branch_id,location_name', 'socialization.branch:id,branch_name')
            ->orderBy('schedule_date')
            ->limit(10)
            ->get();

        $thisMonth = SocializationSchedule::whereMonth('schedule_date', $now->month)
            ->whereYear('schedule_date', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('socialization', fn ($s) => $s->where('branch_id', $branchId)))
            ->count();

        $nextWeek = SocializationSchedule::whereBetween('schedule_date', [
            $now->toDateString(),
            $now->copy()->addWeek()->toDateString(),
        ])->when($branchId, fn ($q) => $q->whereHas('socialization', fn ($s) => $s->where('branch_id', $branchId)))
            ->count();

        return [
            'upcoming' => $upcoming->map(fn ($s) => [
                'location' => $s->socialization?->location_name ?? '-',
                'schedule_date' => $s->schedule_date?->format('Y-m-d'),
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'branch' => $s->socialization?->branch?->branch_name ?? '-',
            ])->toArray(),
            'this_month_count' => $thisMonth,
            'next_week_count' => $nextWeek,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();

        $schedules = SocializationSchedule::whereMonth('schedule_date', $now->month)
            ->whereYear('schedule_date', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('socialization', fn ($s) => $s->where('branch_id', $branchId)))
            ->with('socialization:id,branch_id,location_name', 'socialization.branch:id,branch_name')
            ->orderBy('schedule_date')
            ->get();

        return $schedules->map(fn ($s) => [
            'location' => $s->socialization?->location_name ?? '-',
            'schedule_date' => $s->schedule_date?->format('Y-m-d'),
            'branch' => $s->socialization?->branch?->branch_name ?? '-',
        ])->toArray();
    }
}
