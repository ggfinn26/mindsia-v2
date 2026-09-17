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

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $upcoming = SocializationSchedule::where('scheduled_date', '>=', $now->toDateString())
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('branch:id,branch_name')
            ->orderBy('scheduled_date')
            ->limit(10)
            ->get();

        $thisMonth = SocializationSchedule::whereMonth('scheduled_date', $now->month)
            ->whereYear('scheduled_date', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $nextWeek = SocializationSchedule::whereBetween('scheduled_date', [
            $now->toDateString(),
            $now->copy()->addWeek()->toDateString(),
        ])->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        return [
            'upcoming' => $upcoming->map(fn ($s) => [
                'name' => $s->name ?? '-',
                'scheduled_date' => $s->scheduled_date?->format('Y-m-d'),
                'branch' => $s->branch?->branch_name ?? '-',
                'status' => $s->status ?? 'scheduled',
            ])->toArray(),
            'this_month_count' => $thisMonth,
            'next_week_count' => $nextWeek,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $schedules = SocializationSchedule::whereMonth('scheduled_date', $now->month)
            ->whereYear('scheduled_date', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('branch:id,branch_name')
            ->orderBy('scheduled_date')
            ->get();

        return $schedules->map(fn ($s) => [
            'name' => $s->name ?? '-',
            'scheduled_date' => $s->scheduled_date?->format('Y-m-d'),
            'branch' => $s->branch?->branch_name ?? '-',
            'status' => $s->status ?? '-',
        ])->toArray();
    }
}
