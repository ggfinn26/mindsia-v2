<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Branch;
use App\Models\Program;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ComparableWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'comparable';
    }

    public function getData(array $filters = []): array
    {
        $branches = Branch::where('is_active', true)->get(['id', 'branch_name']);
        $programs = Program::where('is_active', true)->get(['id', 'program_name']);
        
        $branchAId = $filters['branch_a'] ?? $branches->first()?->id;
        $branchBId = $filters['branch_b'] ?? ($branches->count() > 1 ? $branches[1]->id : $branchAId);
        $programId = $filters['program_id'] ?? null;
        $year = (int) ($filters['year'] ?? now()->year);
        $monthStart = (int) ($filters['month_start'] ?? now()->month);
        $monthEnd = (int) ($filters['month_end'] ?? now()->month);
        
        if ($monthEnd < $monthStart) {
            $monthEnd = $monthStart;
        }

        $branchA = Branch::find($branchAId);
        $branchB = Branch::find($branchBId);
        $program = Program::find($programId);

        return [
            'branches' => $branches->toArray(),
            'programs' => $programs->toArray(),
            'branch_a' => $branchA?->toArray(),
            'branch_b' => $branchB?->toArray(),
            'program'  => $program?->toArray(),
            'filters' => [
                'year' => $year,
                'month_start' => $monthStart,
                'month_end' => $monthEnd,
                'program_id' => $programId,
            ],
            'data_a' => $this->getBranchMetrics($branchAId, $monthStart, $monthEnd, $year, $programId),
            'data_b' => $this->getBranchMetrics($branchBId, $monthStart, $monthEnd, $year, $programId),
            'period_label' => $this->formatPeriod($monthStart, $monthEnd, $year),
        ];
    }

    private function getBranchMetrics(?int $branchId, int $start, int $end, int $year, ?int $programId = null): array
    {
        if (!$branchId) {
            return ['members' => 0, 'revenue' => 0, 'programs' => []];
        }

        $membersQuery = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->where('md.branch_id', $branchId)
            ->whereYear('mr.created_at', $year)
            ->whereMonth('mr.created_at', '>=', $start)
            ->whereMonth('mr.created_at', '<=', $end);
            
        if ($programId) {
            $membersQuery->where('mr.program_id', $programId);
        }
        $members = $membersQuery->count();

        $revenueQuery = DB::table('member_payments as mp')
            ->join('members_registration as mr', 'mp.member_registration_id', '=', 'mr.id')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->where('md.branch_id', $branchId)
            ->where('mp.payment_status', 'paid')
            ->whereYear('mp.paid_at', $year)
            ->whereMonth('mp.paid_at', '>=', $start)
            ->whereMonth('mp.paid_at', '<=', $end);
            
        if ($programId) {
            $revenueQuery->where('mr.program_id', $programId);
        }
        $revenue = (float) $revenueQuery->sum('mp.amount');

        $programsQuery = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->where('md.branch_id', $branchId)
            ->whereYear('mr.created_at', $year)
            ->whereMonth('mr.created_at', '>=', $start)
            ->whereMonth('mr.created_at', '<=', $end);
            
        if ($programId) {
            $programsQuery->where('mr.program_id', $programId);
        }
        
        $programsRaw = $programsQuery
            ->selectRaw('p.program_name as name, COUNT(*) as cnt')
            ->groupBy('p.id', 'p.program_name')
            ->orderByDesc('cnt')
            ->limit(5)
            ->get();

        return [
            'members' => $members,
            'revenue' => $revenue,
            'programs' => $programsRaw->toArray(),
        ];
    }
    
    private function formatPeriod(int $start, int $end, int $year): string
    {
        $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
        if ($start === $end) {
            return ($months[$start] ?? '') . ' ' . $year;
        }
        return ($months[$start] ?? '') . ' - ' . ($months[$end] ?? '') . ' ' . $year;
    }

    public function getExportData(array $filters = []): array
    {
        $data = $this->getData($filters);
        
        return [
            [
                'Komparasi' => 'Member Baru',
                'Cabang A (' . ($data['branch_a']['branch_name'] ?? '-') . ')' => $data['data_a']['members'],
                'Cabang B (' . ($data['branch_b']['branch_name'] ?? '-') . ')' => $data['data_b']['members'],
            ],
            [
                'Komparasi' => 'Revenue',
                'Cabang A (' . ($data['branch_a']['branch_name'] ?? '-') . ')' => $data['data_a']['revenue'],
                'Cabang B (' . ($data['branch_b']['branch_name'] ?? '-') . ')' => $data['data_b']['revenue'],
            ],
        ];
    }
}
