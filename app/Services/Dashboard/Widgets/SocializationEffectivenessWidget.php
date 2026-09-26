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

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $totalEvents = Socialization::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $attended = EmployeeSocialization::whereHas('socialization', fn ($q) => $q
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->when($branchId, fn ($bq) => $bq->where('branch_id', $branchId)))
            ->where('status', 'confirmed')
            ->count();

        $converted = MemberRegistration::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('member', fn ($mq) => $mq->where('branch_id', $branchId)))
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

    public function getExportData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();

        $socializations = Socialization::whereYear('created_at', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->withCount(['employeeSocializations as attended_count' => fn ($q) => $q->where('status', 'confirmed')])
            ->orderBy('created_at')
            ->get();

        return $socializations->map(fn ($s) => [
            'event_name' => $s->name ?? '-',
            'created_at' => $s->created_at?->format('Y-m-d'),
            'attended_count' => $s->attended_count,
        ])->toArray();
    }
}
