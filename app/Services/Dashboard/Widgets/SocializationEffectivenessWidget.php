<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeSocialization;
use App\Models\MemberRegistration;
use App\Models\Socialization;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class SocializationEffectivenessWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'socialization_effectiveness';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $totalEvents = Socialization::whereMonth('event_date', $currentMonth)
            ->whereYear('event_date', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $attended = EmployeeSocialization::whereHas('socialization', fn ($q) => $q
            ->whereMonth('event_date', $currentMonth)
            ->whereYear('event_date', $currentYear)
            ->when($branchId, fn ($bq) => $bq->where('branch_id', $branchId)))
            ->where('attended', true)
            ->count();

        $converted = MemberRegistration::whereMonth('registration_date', $currentMonth)
            ->whereYear('registration_date', $currentYear)
            ->whereNotNull('socialization_id')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $conversionRate = $attended > 0
            ? round(($converted / $attended) * 100, 1)
            : 0;

        return [
            'total_events' => $totalEvents,
            'total_attended' => $attended,
            'converted' => $converted,
            'conversion_rate' => $conversionRate,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $socializations = Socialization::whereYear('event_date', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->withCount(['employeeSocializations as attended_count' => fn ($q) => $q->where('attended', true)])
            ->orderBy('event_date')
            ->get();

        return $socializations->map(fn ($s) => [
            'event_name' => $s->name ?? '-',
            'event_date' => $s->event_date?->format('Y-m-d'),
            'attended_count' => $s->attended_count,
        ])->toArray();
    }
}
