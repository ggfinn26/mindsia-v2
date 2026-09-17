<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberPayment;
use App\Models\Program;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class ProgramFinancialPerformanceWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'program_financial_performance';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $programs = Program::where('is_active', true)->get(['id', 'program_name']);

        $programPerformance = $programs->map(function ($program) use ($currentMonth, $currentYear, $branchId) {
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration', fn ($r) => $r
                    ->where('program_id', $program->id)
                    ->when($branchId, fn ($bq) => $bq->where('branch_id', $branchId)))
                ->sum('amount');

            $members = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration', fn ($r) => $r
                    ->where('program_id', $program->id)
                    ->when($branchId, fn ($bq) => $bq->where('branch_id', $branchId)))
                ->distinct('registration_id')
                ->count();

            return [
                'program' => $program->program_name,
                'revenue' => $revenue,
                'member_count' => $members,
            ];
        })->filter(fn ($p) => $p['revenue'] > 0)
            ->sortByDesc('revenue')
            ->values()
            ->toArray();

        return [
            'programs' => $programPerformance,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $programs = Program::where('is_active', true)->get(['id', 'program_name']);

        return $programs->map(function ($program) use ($now, $branchId) {
            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $now->month)
                ->whereYear('paid_at', $now->year)
                ->whereHas('registration', fn ($r) => $r
                    ->where('program_id', $program->id)
                    ->when($branchId, fn ($bq) => $bq->where('branch_id', $branchId)))
                ->sum('amount');

            return [
                'program' => $program->program_name,
                'revenue' => $revenue,
            ];
        })->filter(fn ($p) => $p['revenue'] > 0)
            ->sortByDesc('revenue')
            ->values()
            ->toArray();
    }
}
