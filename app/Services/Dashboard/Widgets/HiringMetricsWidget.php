<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\JobApplication;
use App\Models\JobPermintaanDetail;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class HiringMetricsWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'hiring_metrics';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $baseQuery = JobApplication::whereMonth('applied_at', $currentMonth)
            ->whereYear('applied_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('jobPosting', fn ($j) => $j->where('branch_id', $branchId)));

        $applied = (clone $baseQuery)->count();
        $screened = (clone $baseQuery)->where('status', '!=', 'applied')->count();
        $interviewed = (clone $baseQuery)->whereIn('status', ['interview', 'offering', 'hired'])->count();
        $offered = (clone $baseQuery)->whereIn('status', ['offering', 'hired'])->count();
        $hired = (clone $baseQuery)->where('status', 'hired')->count();

        $screeningRate = $applied > 0 ? round(($screened / $applied) * 100, 1) : 0;
        $interviewRate = $screened > 0 ? round(($interviewed / $screened) * 100, 1) : 0;
        $offerRate = $interviewed > 0 ? round(($offered / $interviewed) * 100, 1) : 0;
        $hireRate = $offered > 0 ? round(($hired / $offered) * 100, 1) : 0;
        $overallConversion = $applied > 0 ? round(($hired / $applied) * 100, 1) : 0;

        $targetHires = JobPermintaanDetail::whereHas('jobPermintaan', fn ($q) => $q
            ->where('status', 'approved'))
            ->when($branchId, fn ($q) => $q->whereHas('jobPermintaan', fn ($bq) => $bq->where('branch_id', $branchId)))
            ->sum('headcount');

        $fulfillmentRate = $targetHires > 0 ? round(($hired / $targetHires) * 100, 1) : 0;

        return [
            'applied' => $applied,
            'screened' => $screened,
            'interviewed' => $interviewed,
            'offered' => $offered,
            'hired' => $hired,
            'screening_rate' => $screeningRate,
            'interview_rate' => $interviewRate,
            'offer_rate' => $offerRate,
            'hire_rate' => $hireRate,
            'overall_conversion' => $overallConversion,
            'target_hires' => $targetHires,
            'fulfillment_rate' => $fulfillmentRate,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $applications = JobApplication::whereYear('applied_at', $now->year)
            ->when($branchId, fn ($q) => $q->whereHas('jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->with(['applicant', 'jobPosting'])
            ->get();

        return $applications->map(fn ($a) => [
            'applicant' => $a->applicant?->full_name ?? '-',
            'position' => $a->jobPosting?->title ?? '-',
            'source' => $a->application_source,
            'status' => $a->status,
            'applied_at' => $a->applied_at?->format('Y-m-d'),
        ])->toArray();
    }
}
