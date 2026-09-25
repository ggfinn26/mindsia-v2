<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeeSocialization;
use App\Models\MemberRegistration;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MySocializationActivityWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'my_socialization_activity';
    }

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $now = Carbon::now();
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        $attended = EmployeeSocialization::where('employee_id', $employeeId)
            ->where('attended', true)
            ->whereHas('socialization', fn ($q) => $q
                ->whereMonth('event_date', $now->month)
                ->whereYear('event_date', $now->year))
            ->count();

        $notAttended = EmployeeSocialization::where('employee_id', $employeeId)
            ->where('attended', false)
            ->whereHas('socialization', fn ($q) => $q
                ->whereMonth('event_date', $now->month)
                ->whereYear('event_date', $now->year))
            ->count();

        $converted = MemberRegistration::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->whereHas('socialization', fn ($q) => $q
                ->whereHas('employeeSocializations', fn ($bq) => $bq->where('employee_id', $employeeId)))
            ->count();

        $conversionRate = $attended > 0
            ? round(($converted / $attended) * 100, 1)
            : 0;

        return [
            'attended' => $attended,
            'not_attended' => $notAttended,
            'converted' => $converted,
            'conversion_rate' => $conversionRate,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        $activities = EmployeeSocialization::where('employee_id', $employeeId)
            ->with('socialization:id,name,event_date')
            ->orderBy('created_at', 'desc')
            ->get();

        return $activities->map(fn ($es) => [
            'event' => $es->socialization?->name ?? '-',
            'event_date' => $es->socialization?->event_date?->format('Y-m-d'),
            'attended' => $es->attended ? 'Ya' : 'Tidak',
        ])->toArray();
    }
}
