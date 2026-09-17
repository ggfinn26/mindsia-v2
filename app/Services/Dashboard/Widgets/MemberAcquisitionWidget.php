<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberRegistration;
use App\Models\ProspectiveMember;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class MemberAcquisitionWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'member_acquisition';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $newMembers = MemberRegistration::whereMonth('registration_date', $currentMonth)
            ->whereYear('registration_date', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $prevMonth = $now->copy()->subMonth();
        $prevNewMembers = MemberRegistration::whereMonth('registration_date', $prevMonth->month)
            ->whereYear('registration_date', $prevMonth->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();

        $trend = $prevNewMembers > 0
            ? round((($newMembers - $prevNewMembers) / $prevNewMembers) * 100, 1)
            : 0;

        $pipeline = ProspectiveMember::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalProspects = array_sum($pipeline);
        $converted = $pipeline['registered'] ?? 0;
        $conversionRate = $totalProspects > 0
            ? round(($converted / $totalProspects) * 100, 1)
            : 0;

        return [
            'new_members' => $newMembers,
            'prev_new_members' => $prevNewMembers,
            'trend_percent' => $trend,
            'pipeline' => $pipeline,
            'total_prospects' => $totalProspects,
            'conversion_rate' => $conversionRate,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $registrations = MemberRegistration::whereYear('registration_date', $now->year)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('member:id,full_name', 'program:id,program_name')
            ->orderBy('registration_date')
            ->get();

        return $registrations->map(fn ($r) => [
            'member' => $r->member?->full_name ?? '-',
            'program' => $r->program?->program_name ?? '-',
            'registration_date' => $r->registration_date?->format('Y-m-d'),
            'status' => $r->status,
        ])->toArray();
    }
}
