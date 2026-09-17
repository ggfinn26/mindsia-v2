<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Models\Program;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class RankingProgramWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'ranking_program';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $programs = Program::where('is_active', true)->get(['id', 'program_name']);

        // Ranking by new members per program
        $ranking = $programs->map(function ($program) use ($currentMonth, $currentYear, $branchId) {
            $newMembers = MemberRegistration::whereMonth('registration_date', $currentMonth)
                ->whereYear('registration_date', $currentYear)
                ->where('program_id', $program->id)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->count();

            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration', fn ($r) => $r
                    ->where('program_id', $program->id)
                    ->when($branchId, fn ($bq) => $bq->where('branch_id', $branchId)))
                ->sum('amount');

            return [
                'program' => $program->program_name,
                'new_members' => $newMembers,
                'revenue' => $revenue,
            ];
        })->sortByDesc('new_members')->values()->toArray();

        return [
            'ranking' => $ranking,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $programs = Program::where('is_active', true)->get(['id', 'program_name']);

        return $programs->map(function ($program) use ($now, $branchId) {
            $newMembers = MemberRegistration::whereMonth('registration_date', $now->month)
                ->whereYear('registration_date', $now->year)
                ->where('program_id', $program->id)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->count();

            return [
                'program' => $program->program_name,
                'new_members' => $newMembers,
            ];
        })->sortByDesc('new_members')->values()->toArray();
    }
}
