<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\ApplicantPsikotest;
use App\Models\JobApplication;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class CandidateScreeningWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'candidate_screening';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $screeningQuery = JobApplication::where('status', 'screening')
            ->when($branchId, fn ($q) => $q->whereHas('jobPosting', fn ($j) => $j->where('branch_id', $branchId)));

        $inScreening = (clone $screeningQuery)->count();

        $totalScreenedThisMonth = JobApplication::whereHas('stages', fn ($q) => $q
            ->where('stage', 'screening')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear))
            ->when($branchId, fn ($q) => $q->whereHas('jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->count();

        $passedScreening = JobApplication::whereHas('stages', fn ($q) => $q
            ->where('stage', 'screening')
            ->where('result', 'passed')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear))
            ->when($branchId, fn ($q) => $q->whereHas('jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->count();

        $passRate = $totalScreenedThisMonth > 0
            ? round(($passedScreening / $totalScreenedThisMonth) * 100, 1)
            : 0;

        $psikotestCompleted = ApplicantPsikotest::whereMonth('psikotest_date', $currentMonth)
            ->whereYear('psikotest_date', $currentYear)
            ->whereNotNull('psikotest_score')
            ->when($branchId, fn ($q) => $q->whereHas('application.jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->count();

        $avgPsikotestScore = ApplicantPsikotest::whereMonth('psikotest_date', $currentMonth)
            ->whereYear('psikotest_date', $currentYear)
            ->whereNotNull('psikotest_score')
            ->when($branchId, fn ($q) => $q->whereHas('application.jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->avg('psikotest_score') ?? 0;

        return [
            'in_screening' => $inScreening,
            'total_screened' => $totalScreenedThisMonth,
            'passed_screening' => $passedScreening,
            'pass_rate' => $passRate,
            'psikotest_completed' => $psikotestCompleted,
            'avg_psikotest_score' => round($avgPsikotestScore, 1),
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $applications = JobApplication::whereHas('stages', fn ($q) => $q
            ->where('stage', 'screening')
            ->whereYear('created_at', $now->year))
            ->when($branchId, fn ($q) => $q->whereHas('jobPosting', fn ($j) => $j->where('branch_id', $branchId)))
            ->with(['applicant', 'jobPosting', 'psikotest'])
            ->get();

        return $applications->map(fn ($a) => [
            'applicant' => $a->applicant?->full_name ?? '-',
            'position' => $a->jobPosting?->title ?? '-',
            'status' => $a->status,
            'psikotest_score' => $a->psikotest?->psikotest_score ?? '-',
            'screening_result' => $a->stages->where('stage', 'screening')->first()?->result ?? '-',
        ])->toArray();
    }
}
