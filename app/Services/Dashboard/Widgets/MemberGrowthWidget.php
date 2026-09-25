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
        $currentMonth = (int) ($filters['month'] ?? now()->month);
        $currentYear = (int) ($filters['year'] ?? now()->year);
        $now = Carbon::create($currentYear, $currentMonth, 1);
        $prev = $now->copy()->subMonth();
        $yoy = $now->copy()->subYear();

        $branches = Branch::with('area')
            ->where('is_active', true)
            ->when($branchIdFilter, fn ($q) => $q->where('id', $branchIdFilter))
            ->get();
        $branchIds = $branches->pluck('id')->toArray();

        // branch_id is on members_data, not members_registration → always join
        $memberCountByBranch = function (int $m, int $y) use ($branchIds) {
            return DB::table('members_registration as mr')
                ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
                ->whereIn('md.branch_id', $branchIds)
                ->whereYear('mr.created_at', $y)
                ->whereMonth('mr.created_at', $m)
                ->groupBy('md.branch_id')
                ->selectRaw('md.branch_id, COUNT(*) as cnt')
                ->pluck('cnt', 'branch_id');
        };

        $thisM = $memberCountByBranch($currentMonth, $currentYear);
        $prevM = $memberCountByBranch($prev->month, $prev->year);
        $yoyM = $memberCountByBranch($yoy->month, $yoy->year);

        $revByBranch = DB::table('member_payments as mp')
            ->join('members_registration as mr', 'mp.member_registration_id', '=', 'mr.id')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->whereIn('md.branch_id', $branchIds)
            ->where('mp.payment_status', 'paid')
            ->whereYear('mp.paid_at', $currentYear)
            ->whereMonth('mp.paid_at', $currentMonth)
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

        $byBranch = $branches->map(function ($branch) use ($thisM, $prevM, $yoyM, $revByBranch) {
            $curr = (int) ($thisM[$branch->id] ?? 0);
            $p = (int) ($prevM[$branch->id] ?? 0);
            $y = (int) ($yoyM[$branch->id] ?? 0);

            return [
                'name' => $branch->branch_name,
                'area_name' => $branch->area?->name ?? '-',
                'this_month' => $curr,
                'mom_pct' => $p > 0 ? round((($curr - $p) / $p) * 100, 1) : 0,
                'yoy_pct' => $y > 0 ? round((($curr - $y) / $y) * 100, 1) : 0,
                'revenue' => (float) ($revByBranch[$branch->id] ?? 0),
            ];
        })->sortByDesc('this_month')->values()->toArray();

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
        }
        $byArea = collect($areaMap)->map(function ($a) use ($areaPrevM, $areaYoyM) {
            $p = $areaPrevM[$a['name']] ?? 0;
            $y = $areaYoyM[$a['name']] ?? 0;

            return array_merge($a, [
                'mom_pct' => $p > 0 ? round((($a['this_month'] - $p) / $p) * 100, 1) : 0,
                'yoy_pct' => $y > 0 ? round((($a['this_month'] - $y) / $y) * 100, 1) : 0,
            ]);
        })->sortByDesc('this_month')->values()->toArray();

        $memberByProgram = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->whereIn('md.branch_id', $branchIds)
            ->whereYear('mr.created_at', $currentYear)
            ->whereMonth('mr.created_at', $currentMonth)
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

        $byProgram = Program::where('is_active', true)->get(['id', 'program_name'])
            ->map(fn ($p) => [
                'program' => $p->program_name,
                'member_count' => (int) ($memberByProgram[$p->id] ?? 0),
                'revenue' => (float) ($revByProgram[$p->id] ?? 0),
            ])
            ->sortByDesc('member_count')
            ->values()
            ->toArray();

        $momPct = $natPrev > 0 ? round((($natThis - $natPrev) / $natPrev) * 100, 1) : 0;
        $yoyPct = $natYoy > 0 ? round((($natThis - $natYoy) / $natYoy) * 100, 1) : 0;

        return [
            'period' => $now->translatedFormat('F Y'),
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
            'monthly_trend' => $monthlyTrend,
            'branch_count' => count($byBranch),
            'area_count' => count($byArea),
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
