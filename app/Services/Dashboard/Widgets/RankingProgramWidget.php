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

    public function getData(array $filters = []): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $currentMonth = (int) ($filters['month'] ?? now()->month);
        $currentYear = (int) ($filters['year'] ?? now()->year);
        $now = Carbon::create($currentYear, $currentMonth, 1);

        $programs = Program::where('is_active', true)->get(['id', 'program_name']);

        $ranking = $programs->map(function ($program) use ($currentMonth, $currentYear, $branchId) {
            $memberCount = MemberRegistration::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('program_id', $program->id)
                ->when($branchId, fn ($q) => $q->whereHas('memberData', fn ($md) => $md->where('branch_id', $branchId)))
                ->count();

            $revenue = MemberPayment::where('payment_status', 'paid')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->whereHas('registration', fn ($r) => $r
                    ->where('program_id', $program->id)
                    ->when($branchId, fn ($bq) => $bq->whereHas('memberData', fn ($md) => $md->where('branch_id', $branchId))))
                ->sum('amount');

            return [
                'program' => $program->program_name,
                'member_count' => $memberCount,
                'revenue' => $revenue,
            ];
        })->sortByDesc('member_count')->values()->toArray();

        return [
            'programs' => $ranking,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $data = $this->getData($filters);

        return array_map(fn ($p) => [
            'program' => $p['program'],
            'member_baru' => $p['member_count'],
            'revenue' => $p['revenue'],
        ], $data['programs']);
    }
}
