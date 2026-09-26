<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Branch;
use App\Models\Program;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MemberGrowthWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'member_growth';
    }

    public function getData(array $filters = []): array
    {
        $branchIdFilter = $filters['branch_id'] ?? null;
        $periodType = $filters['period_type'] ?? 'monthly';
        $currentMonth = (int) ($filters['month'] ?? now()->month);
        $currentYear = (int) ($filters['year'] ?? now()->year);
        $semester = (int) ($filters['semester'] ?? 1);
        
        $now = Carbon::create($currentYear, $currentMonth, 1);
        $prev = $now->copy()->subMonth();
        $yoy = $now->copy()->subYear();

        $branches = Branch::with('area')
            ->where('is_active', true)
            ->when($branchIdFilter, fn ($q) => $q->where('id', $branchIdFilter))
            ->get();
        $branchIds = $branches->pluck('id')->toArray();

        // Helper to apply time filter based on period type
        $applyTimeFilter = function($query, $pType, $m, $y, $sem, $dateCol = 'mr.created_at') {
            $query->whereYear($dateCol, $y);
            if ($pType === 'monthly') {
                $query->whereMonth($dateCol, $m);
            } elseif ($pType === 'semester') {
                if ($sem === 1) {
                    $query->whereMonth($dateCol, '>=', 1)->whereMonth($dateCol, '<=', 6);
                } else {
                    $query->whereMonth($dateCol, '>=', 7)->whereMonth($dateCol, '<=', 12);
                }
            }
            return $query;
        };

        // branch_id is on members_data, not members_registration → always join
        $memberCountByBranch = function ($pType, $m, $y, $sem) use ($branchIds, $applyTimeFilter) {
            $q = DB::table('members_registration as mr')
                ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
                ->whereIn('md.branch_id', $branchIds)
                ->groupBy('md.branch_id')
                ->selectRaw('md.branch_id, COUNT(*) as cnt');
            return $applyTimeFilter($q, $pType, $m, $y, $sem)->pluck('cnt', 'branch_id');
        };

        $thisM = $memberCountByBranch($periodType, $currentMonth, $currentYear, $semester);
        $prevM = $periodType === 'monthly' ? $memberCountByBranch('monthly', $prev->month, $prev->year, 1) : collect();
        $yoyM = $memberCountByBranch($periodType, $currentMonth, $yoy->year, $semester);

        $revByBranch = DB::table('member_payments as mp')
            ->join('members_registration as mr', 'mp.member_registration_id', '=', 'mr.id')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->whereIn('md.branch_id', $branchIds)
            ->where('mp.payment_status', 'paid')
            ;
        $revByBranch = $applyTimeFilter($revByBranch, $periodType, $currentMonth, $currentYear, $semester, 'mp.paid_at')
            ->groupBy('md.branch_id')
            ->selectRaw('md.branch_id, SUM(mp.amount) as total')
            ->pluck('total', 'branch_id');

        $natThis = $thisM->sum();
        $natPrev = $prevM->sum();
        $natYoy = $yoyM->sum();

        $natCumulative = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->whereIn('md.branch_id', $branchIds)
            ->count();

        $monthlyTrend = collect(range(1, $currentMonth))->map(fn ($m) => [
            'month' => $m,
            'count' => DB::table('members_registration as mr')
                ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
                ->whereIn('md.branch_id', $branchIds)
                ->whereYear('mr.created_at', $currentYear)
                ->whereMonth('mr.created_at', $m)
                ->count(),
        ])->toArray();

        $memberByBranchAndProgram = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->whereIn('md.branch_id', $branchIds)
            ;
        $memberByBranchAndProgram = $applyTimeFilter($memberByBranchAndProgram, $periodType, $currentMonth, $currentYear, $semester, 'mr.created_at')
            ->groupBy('md.branch_id', 'p.program_name')
            ->selectRaw('md.branch_id, p.program_name, COUNT(*) as cnt')
            ->get()
            ->groupBy('branch_id');

        $demographicByBranch = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('institutions as i', 'md.institution_id', '=', 'i.id')
            ->whereIn('md.branch_id', $branchIds)
            ;
        $demographicByBranch = $applyTimeFilter($demographicByBranch, $periodType, $currentMonth, $currentYear, $semester, 'mr.created_at')
            ->groupBy('md.branch_id', 'i.jenjang_institution')
            ->selectRaw('md.branch_id, i.jenjang_institution, COUNT(*) as cnt')
            ->get()
            ->groupBy('branch_id');

        $byBranch = $branches->map(function ($branch) use ($thisM, $prevM, $yoyM, $revByBranch, $memberByBranchAndProgram, $demographicByBranch) {
            $curr = (int) ($thisM[$branch->id] ?? 0);
            $p = (int) ($prevM[$branch->id] ?? 0);
            $y = (int) ($yoyM[$branch->id] ?? 0);
            
            $programs = $memberByBranchAndProgram->get($branch->id, collect())->map(function($i) {
                return ['name' => $i->program_name, 'count' => $i->cnt];
            })->sortByDesc('count')->values()->toArray();

            $demographics = $demographicByBranch->get($branch->id, collect())->map(function($d) {
                return ['name' => $d->jenjang_institution ?: 'TIDAK DIKETAHUI', 'count' => $d->cnt];
            })->sortByDesc('count')->values()->toArray();

            return [
                'id' => $branch->id,
                'name' => $branch->branch_name,
                'area_name' => $branch->area?->name ?? '-',
                'this_month' => $curr,
                'mom_pct' => $p > 0 ? round((($curr - $p) / $p) * 100, 1) : 0,
                'yoy_pct' => $y > 0 ? round((($curr - $y) / $y) * 100, 1) : 0,
                'revenue' => (float) ($revByBranch[$branch->id] ?? 0),
                'programs' => $programs,
                'demographics' => $demographics,
            ];
        })->sortByDesc('this_month')->values()->toArray();

                $paymentsByArea = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('branches as b', 'md.branch_id', '=', 'b.id')
            ->join('areas as a', 'b.areas_id', '=', 'a.id')
            ->whereIn('md.branch_id', $branchIds);
        $paymentsByArea = $applyTimeFilter($paymentsByArea, $periodType, $currentMonth, $currentYear, $semester, 'mr.created_at')
            ->groupBy('a.name', 'mr.payment_status', 'mr.installment_type')
            ->selectRaw('a.name as area_name, mr.payment_status, mr.installment_type, COUNT(*) as cnt')
            ->get()
            ->groupBy('area_name');
        $branchAreaMap = $branches->pluck('area.name', 'id');
        $areaPrevM = [];
        $areaYoyM = [];
        foreach ($prevM as $bid => $cnt) {
            $area = $branchAreaMap[$bid] ?? '-';
            $areaPrevM[$area] = ($areaPrevM[$area] ?? 0) + $cnt;
        }
        foreach ($yoyM as $bid => $cnt) {
            $area = $branchAreaMap[$bid] ?? '-';
            $areaYoyM[$area] = ($areaYoyM[$area] ?? 0) + $cnt;
        }
        $areaMap = [];
        foreach ($byBranch as $b) {
            $a = $b['area_name'];
            $areaMap[$a]['name'] = $a;
            $areaMap[$a]['this_month'] = ($areaMap[$a]['this_month'] ?? 0) + $b['this_month'];
            $areaMap[$a]['revenue'] = ($areaMap[$a]['revenue'] ?? 0) + $b['revenue'];
            $areaMap[$a]['branch_count'] = ($areaMap[$a]['branch_count'] ?? 0) + 1;
            if (!isset($areaMap[$a]['programs'])) $areaMap[$a]['programs'] = [];
            
            foreach ($b['programs'] as $prog) {
                $pName = $prog['name'];
                $areaMap[$a]['programs'][$pName] = ($areaMap[$a]['programs'][$pName] ?? 0) + $prog['count'];
            }
        }
        
        $byArea = collect($areaMap)->map(function ($a) use ($areaPrevM, $areaYoyM, $paymentsByArea) {
            $p = $areaPrevM[$a['name']] ?? 0;
            $y = $areaYoyM[$a['name']] ?? 0;

            $sortedPrograms = collect($a['programs'])->map(function($count, $name) {
                return ['name' => $name, 'count' => $count];
            })->sortByDesc('count')->values()->toArray();

            $payments = $paymentsByArea->get($a['name'], collect())->values()->toArray();
            return array_merge($a, [
                'payments' => $payments,
                'mom_pct' => $p > 0 ? round((($a['this_month'] - $p) / $p) * 100, 1) : 0,
                'yoy_pct' => $y > 0 ? round((($a['this_month'] - $y) / $y) * 100, 1) : 0,
                'programs' => $sortedPrograms,
            ]);
        })->sortByDesc('this_month')->values()->toArray();

        $memberByProgram = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->whereIn('md.branch_id', $branchIds)
            ;
        $memberByProgram = $applyTimeFilter($memberByProgram, $periodType, $currentMonth, $currentYear, $semester, 'mr.created_at')
            ->groupBy('mr.program_id')
            ->selectRaw('mr.program_id, COUNT(*) as cnt')
            ->pluck('cnt', 'program_id');

        $revByProgram = DB::table('member_payments as mp')
            ->join('members_registration as mr', 'mp.member_registration_id', '=', 'mr.id')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->whereIn('md.branch_id', $branchIds)
            ->where('mp.payment_status', 'paid')
            ->whereYear('mp.paid_at', $currentYear)
            ->whereMonth('mp.paid_at', $currentMonth)
            ->groupBy('mr.program_id')
            ->selectRaw('mr.program_id, SUM(mp.amount) as total')
            ->pluck('total', 'program_id');

        // NEW: Payment Scheme Breakdown
        $paymentSchemeStats = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->whereIn('md.branch_id', $branchIds)
            ;
        $paymentSchemeStats = $applyTimeFilter($paymentSchemeStats, $periodType, $currentMonth, $currentYear, $semester, 'mr.created_at')
            ->selectRaw('p.program_name as program, mr.payment_status, mr.installment_type, COUNT(*) as cnt')
            ->groupBy('p.program_name', 'mr.payment_status', 'mr.installment_type')
            ->get()
            ->toArray();

        // NEW: Institution Demographic Breakdown
        $demographicStats = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('institutions as i', 'md.institution_id', '=', 'i.id')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->whereIn('md.branch_id', $branchIds)
            ;
        $demographicStats = $applyTimeFilter($demographicStats, $periodType, $currentMonth, $currentYear, $semester, 'mr.created_at')
            ->selectRaw('p.program_name as program, i.jenjang_institution, COUNT(*) as cnt')
            ->groupBy('p.program_name', 'i.jenjang_institution')
            ->get()
            ->toArray();

        $byProgram = Program::where('is_active', true)->get(['id', 'program_name'])
            ->map(fn ($p) => [
                'program' => $p->program_name,
                'member_count' => (int) ($memberByProgram[$p->id] ?? 0),
                'revenue' => (float) ($revByProgram[$p->id] ?? 0),
                'demographics' => array_values(array_filter($demographicStats, fn($d) => $d->program === $p->program_name)),
                'payments' => array_values(array_filter($paymentSchemeStats, fn($pay) => $pay->program === $p->program_name)),
            ])
            ->sortByDesc('member_count')
            ->values()
            ->toArray();

        $momPct = $natPrev > 0 ? round((($natThis - $natPrev) / $natPrev) * 100, 1) : 0;
        $yoyPct = $natYoy > 0 ? round((($natThis - $natYoy) / $natYoy) * 100, 1) : 0;

        $memberByDemografi = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('institutions as i', 'md.institution_id', '=', 'i.id')
            ->whereIn('md.branch_id', $branchIds);
        $memberByDemografi = $applyTimeFilter($memberByDemografi, $periodType, $currentMonth, $currentYear, $semester, 'mr.created_at')
            ->groupBy('i.jenjang_institution')
            ->selectRaw('i.jenjang_institution, COUNT(*) as cnt')
            ->pluck('cnt', 'jenjang_institution');

        $progByDemografi = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->join('institutions as i', 'md.institution_id', '=', 'i.id')
            ->whereIn('md.branch_id', $branchIds);
        $progByDemografi = $applyTimeFilter($progByDemografi, $periodType, $currentMonth, $currentYear, $semester, 'mr.created_at')
            ->groupBy('i.jenjang_institution', 'p.program_name')
            ->selectRaw('i.jenjang_institution, p.program_name, COUNT(*) as cnt')
            ->get()
            ->groupBy('jenjang_institution');

        $byDemografi = $memberByDemografi->map(function($cnt, $jenjang) use ($progByDemografi) {
            $programs = $progByDemografi->get($jenjang, collect())->map(function($p) {
                return ['name' => $p->program_name, 'count' => $p->cnt];
            })->sortByDesc('count')->values()->toArray();

            return [
                'name' => $jenjang ?: 'TIDAK DIKETAHUI',
                'this_month' => $cnt,
                'programs' => $programs,
            ];
        })->sortByDesc('this_month')->values()->toArray();

        $periodString = $now->translatedFormat('F Y');
        if ($periodType === 'yearly') {
            $periodString = 'Tahun ' . $currentYear;
        } elseif ($periodType === 'semester') {
            $periodString = 'Semester ' . $semester . ' ' . $currentYear;
        }

        return [
            'period' => $periodString,
            'national' => [
                'this_month' => $natThis,
                'last_month' => $natPrev,
                'yoy' => $natYoy,
                'cumulative' => $natCumulative,
                'mom_pct' => $momPct,
                'yoy_pct' => $yoyPct,
            ],
            'by_area' => $byArea,
            'by_branch' => $byBranch,
            'by_program' => $byProgram,
            'by_demografi' => $byDemografi,
            'monthly_trend' => $monthlyTrend,

            'branch_count' => count($byBranch),
            'area_count' => count($byArea),
            'filters' => [
                'branch_id' => $branchIdFilter,
                'month' => $currentMonth,
                'year' => $currentYear,
            ],
            // legacy keys
            'total_active' => $natCumulative,
            'yoy_growth' => $yoyPct,
        ];
    }

    public function getExportData(array $filters = []): array
    {
        $data = $this->getData($filters);

        return array_map(fn ($b) => [
            'cabang' => $b['name'],
            'area' => $b['area_name'],
            'member_baru' => $b['this_month'],
            'mom_pct' => $b['mom_pct'],
            'yoy_pct' => $b['yoy_pct'],
            'revenue' => $b['revenue'],
        ], $data['by_branch']);
    }
}
