<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Area;
use App\Models\Branch;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MarketingProgressWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'marketing_progress';
    }

    public function getData(array $filters = []): array
    {
        $branchIdFilter = $filters['branch_id'] ?? null;
        $areaIdFilter = $filters['area_id'] ?? null;
        $periodType = $filters['period_type'] ?? 'monthly';
        $currentMonth = (int) ($filters['month'] ?? now()->month);
        $currentYear = (int) ($filters['year'] ?? now()->year);
        $semester = (int) ($filters['semester'] ?? 1);

        $branches = Branch::with('area')
            ->where('is_active', true)
            ->when($branchIdFilter, fn ($q) => $q->where('id', $branchIdFilter))
            ->when($areaIdFilter && ! $branchIdFilter, fn ($q) => $q->where('areas_id', $areaIdFilter))
            ->get();

        $branchIds = $branches->pluck('id')->toArray();

        if (empty($branchIds)) {
            return $this->emptyResult($currentMonth, $currentYear, $periodType, $semester, $branchIdFilter, $areaIdFilter);
        }

        $omzetByBranch = $this->omzetByBranch($branchIds, $periodType, $currentMonth, $currentYear, $semester);
        $prospectiveByBranch = $this->perfMetricByBranch($branchIds, 'SUM(mp.prospective_members_count)', $periodType, $currentMonth, $currentYear, $semester);
        $socByBranch = $this->socializationsByBranch($branchIds, $periodType, $currentMonth, $currentYear, $semester);
        $memberByBranch = $this->membersByBranch($branchIds, $periodType, $currentMonth, $currentYear, $semester);

        $byBranch = $branches->map(fn ($branch) => [
            'id' => $branch->id,
            'name' => $branch->branch_name,
            'area_name' => $branch->area?->name ?? '-',
            'omzet' => (float) ($omzetByBranch[$branch->id] ?? 0),
            'sosialisasi' => (int) ($socByBranch[$branch->id] ?? 0),
            'member' => (int) ($memberByBranch[$branch->id] ?? 0),
            'prospective' => (int) ($prospectiveByBranch[$branch->id] ?? 0),
        ])->sortByDesc('omzet')->values()->toArray();

        $byArea = $this->aggregateByArea($byBranch);
        $byEmployee = $this->employeeData($branchIds, $periodType, $currentMonth, $currentYear, $semester, $branches);

        $national = [
            'omzet' => array_sum(array_column($byBranch, 'omzet')),
            'sosialisasi' => array_sum(array_column($byBranch, 'sosialisasi')),
            'member' => array_sum(array_column($byBranch, 'member')),
            'prospective' => array_sum(array_column($byBranch, 'prospective')),
        ];

        $areas = Area::orderBy('name')->get(['id', 'name']);

        return [
            'period' => $this->periodLabel($periodType, $currentMonth, $currentYear, $semester),
            'national' => $national,
            'by_area' => $byArea,
            'by_branch' => $byBranch,
            'by_employee' => $byEmployee,
            'areas' => $areas->toArray(),
            'branches' => Branch::where('is_active', true)->orderBy('branch_name')->get(['id', 'branch_name'])->toArray(),
            'filters' => [
                'branch_id' => $branchIdFilter,
                'area_id' => $areaIdFilter,
                'month' => $currentMonth,
                'year' => $currentYear,
                'period_type' => $periodType,
                'semester' => $semester,
            ],
        ];
    }

    private function omzetByBranch(array $branchIds, string $pType, int $m, int $y, int $sem): Collection
    {
        $q = DB::table('marketing_kpi_snapshots as mks')
            ->join('employees as e', 'mks.employee_id', '=', 'e.id')
            ->whereIn('e.branch_id', $branchIds)
            ->where('mks.period_year', $y);

        if ($pType === 'monthly') {
            $q->where('mks.period_month', $m);
        } elseif ($pType === 'semester') {
            $q->whereIn('mks.period_month', $sem === 1 ? [1, 2, 3, 4, 5, 6] : [7, 8, 9, 10, 11, 12]);
        }

        return $q->groupBy('e.branch_id')
            ->selectRaw('e.branch_id, SUM(mks.registration_value_actual) as total')
            ->pluck('total', 'branch_id');
    }

    private function perfMetricByBranch(array $branchIds, string $selectExpr, string $pType, int $m, int $y, int $sem): Collection
    {
        $q = DB::table('marketing_performances as mp')
            ->join('employees as e', 'mp.employee_id', '=', 'e.id')
            ->whereIn('e.branch_id', $branchIds)
            ->where('mp.period_year', $y);

        if ($pType === 'monthly') {
            $q->where('mp.period_month', $m);
        } elseif ($pType === 'semester') {
            $months = $sem === 1 ? [1, 2, 3, 4, 5, 6] : [7, 8, 9, 10, 11, 12];
            $q->whereIn('mp.period_month', $months);
        }

        return $q->groupBy('e.branch_id')
            ->selectRaw("e.branch_id, {$selectExpr} as total")
            ->pluck('total', 'branch_id');
    }

    private function socializationsByBranch(array $branchIds, string $pType, int $m, int $y, int $sem): Collection
    {
        $q = DB::table('socializations')
            ->whereIn('branch_id', $branchIds)
            ->where('status', '!=', 'cancelled')
            ->whereYear('created_at', $y);

        if ($pType === 'monthly') {
            $q->whereMonth('created_at', $m);
        } elseif ($pType === 'semester') {
            if ($sem === 1) {
                $q->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
            } else {
                $q->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
            }
        }

        return $q->groupBy('branch_id')
            ->selectRaw('branch_id, COUNT(*) as cnt')
            ->pluck('cnt', 'branch_id');
    }

    private function membersByBranch(array $branchIds, string $pType, int $m, int $y, int $sem): Collection
    {
        $q = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->whereIn('md.branch_id', $branchIds)
            ->whereYear('mr.created_at', $y);

        if ($pType === 'monthly') {
            $q->whereMonth('mr.created_at', $m);
        } elseif ($pType === 'semester') {
            if ($sem === 1) {
                $q->whereMonth('mr.created_at', '>=', 1)->whereMonth('mr.created_at', '<=', 6);
            } else {
                $q->whereMonth('mr.created_at', '>=', 7)->whereMonth('mr.created_at', '<=', 12);
            }
        }

        return $q->groupBy('md.branch_id')
            ->selectRaw('md.branch_id, COUNT(*) as cnt')
            ->pluck('cnt', 'branch_id');
    }

    private function employeeData(array $branchIds, string $pType, int $m, int $y, int $sem, Collection $branches): array
    {
        $branchNameMap = $branches->pluck('branch_name', 'id');

        $employees = DB::table('employees as e')
            ->where(fn ($q) => $q->whereIn('e.branch_id', $branchIds)->orWhereNull('e.branch_id'))
            ->where('e.is_active', true)
            ->select('e.id', 'e.full_name', 'e.branch_id')
            ->get()
            ->keyBy('id');

        if ($employees->isEmpty()) {
            return [];
        }

        $empIds = $employees->keys()->toArray();

        // Omzet dari marketing_kpi_snapshots.registration_value_actual
        $omzetQ = DB::table('marketing_kpi_snapshots as mks')
            ->whereIn('mks.employee_id', $empIds)
            ->where('mks.period_year', $y);
        if ($pType === 'monthly') {
            $omzetQ->where('mks.period_month', $m);
        } elseif ($pType === 'semester') {
            $omzetQ->whereIn('mks.period_month', $sem === 1 ? [1, 2, 3, 4, 5, 6] : [7, 8, 9, 10, 11, 12]);
        }
        $omzetMap = $omzetQ->groupBy('mks.employee_id')
            ->selectRaw('mks.employee_id, SUM(mks.registration_value_actual) as omzet')
            ->get()->keyBy('employee_id');

        // Target dari marketing_targets (per-employee override)
        $targetQ = DB::table('marketing_targets as mt')
            ->whereIn('mt.employee_id', $empIds)
            ->where('mt.period_year', $y);
        if ($pType === 'monthly') {
            $targetQ->where('mt.period_month', $m);
        } elseif ($pType === 'semester') {
            $targetQ->whereIn('mt.period_month', $sem === 1 ? [1, 2, 3, 4, 5, 6] : [7, 8, 9, 10, 11, 12]);
        }
        $targetMap = $targetQ->groupBy('mt.employee_id')
            ->selectRaw('mt.employee_id, SUM(mt.omzet_target) as omzet_target, SUM(mt.classes_target) as classes_target')
            ->get()->keyBy('employee_id');

        // Fallback: global defaults per position
        $globalDefaults = DB::table('marketing_target_global_defaults')
            ->get()->keyBy('position_id');

        // Map employee_id → position_id untuk fallback
        $empPositionMap = DB::table('employment_status as es')
            ->whereIn('es.employees_id', $empIds)
            ->select('es.employees_id', 'es.position_id')
            ->get()->keyBy('employees_id');

        // Member baru dari members_data.referred_by_employee_id
        $memberQ = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->whereIn('md.referred_by_employee_id', $empIds)
            ->whereYear('mr.created_at', $y);
        if ($pType === 'monthly') {
            $memberQ->whereMonth('mr.created_at', $m);
        } elseif ($pType === 'semester') {
            $memberQ->whereIn(DB::raw('MONTH(mr.created_at)'), $sem === 1 ? [1, 2, 3, 4, 5, 6] : [7, 8, 9, 10, 11, 12]);
        }
        $memberMap = $memberQ->groupBy('md.referred_by_employee_id')
            ->selectRaw('md.referred_by_employee_id as employee_id, COUNT(*) as cnt')
            ->get()->keyBy('employee_id');

        // Kelas dari SUM(employee_socializations.classes_obtained) per periode
        $kelasQ = DB::table('employee_socializations as es')
            ->join('socializations as s', 'es.socialization_id', '=', 's.id')
            ->whereIn('es.employee_id', $empIds)
            ->where('s.status', '!=', 'cancelled')
            ->whereYear('s.created_at', $y);
        if ($pType === 'monthly') {
            $kelasQ->whereMonth('s.created_at', $m);
        } elseif ($pType === 'semester') {
            $kelasQ->whereIn(DB::raw('MONTH(s.created_at)'), $sem === 1 ? [1, 2, 3, 4, 5, 6] : [7, 8, 9, 10, 11, 12]);
        }
        $kelasMap = $kelasQ->groupBy('es.employee_id')
            ->selectRaw('es.employee_id, SUM(es.classes_obtained) as cnt')
            ->get()->keyBy('employee_id');

        // Prospek dari prospective_members.captured_by_employee_id
        $prospectQ = DB::table('prospective_members as pm')
            ->whereIn('pm.captured_by_employee_id', $empIds)
            ->whereYear('pm.created_at', $y);
        if ($pType === 'monthly') {
            $prospectQ->whereMonth('pm.created_at', $m);
        } elseif ($pType === 'semester') {
            $prospectQ->whereIn(DB::raw('MONTH(pm.created_at)'), $sem === 1 ? [1, 2, 3, 4, 5, 6] : [7, 8, 9, 10, 11, 12]);
        }
        $prospectMap = $prospectQ->groupBy('pm.captured_by_employee_id')
            ->selectRaw('pm.captured_by_employee_id as employee_id, COUNT(*) as cnt')
            ->get()->keyBy('employee_id');

        // Period position defaults
        $periodPositionDefaults = DB::table('marketing_target_defaults')
            ->where('period_year', $y)
            ->when($pType === 'monthly', fn ($q) => $q->where('period_month', $m))
            ->when($pType === 'semester', fn ($q) => $q->whereIn('period_month', $sem === 1 ? [1, 2, 3, 4, 5, 6] : [7, 8, 9, 10, 11, 12]))
            ->get()
            ->groupBy('position_id')
            ->map(fn ($rows) => (object) [
                'omzet_target' => $rows->sum('omzet_target'),
                'classes_target' => $rows->sum('classes_target'),
            ]);

        return $employees->map(function ($e) use ($omzetMap, $targetMap, $kelasMap, $memberMap, $prospectMap, $branchNameMap, $empPositionMap, $globalDefaults, $periodPositionDefaults) {
            $positionId = $empPositionMap->get($e->id)?->position_id;

            // Hierarki: employee override → period position default → global default → 0
            if ($targetMap->has($e->id)) {
                $omzetTarget = (float) $targetMap[$e->id]->omzet_target;
                $kelasTarget = (int) $targetMap[$e->id]->classes_target;
            } elseif ($positionId && $periodPositionDefaults->has($positionId)) {
                $omzetTarget = (float) $periodPositionDefaults[$positionId]->omzet_target;
                $kelasTarget = (int) $periodPositionDefaults[$positionId]->classes_target;
            } elseif ($positionId && $globalDefaults->has($positionId)) {
                $omzetTarget = (float) $globalDefaults[$positionId]->omzet_target;
                $kelasTarget = (int) $globalDefaults[$positionId]->classes_target;
            } else {
                $omzetTarget = 0.0;
                $kelasTarget = 0;
            }

            return [
                'id' => $e->id,
                'name' => $e->full_name,
                'branch_name' => $branchNameMap[$e->branch_id] ?? '-',
                'omzet' => (float) ($omzetMap[$e->id]?->omzet ?? 0),
                'omzet_target' => $omzetTarget,
                'kelas' => (int) ($kelasMap[$e->id]?->cnt ?? 0),
                'kelas_target' => $kelasTarget,
                'member' => (int) ($memberMap[$e->id]?->cnt ?? 0),
                'prospective' => (int) ($prospectMap[$e->id]?->cnt ?? 0),
            ];
        })->sortByDesc('omzet')->values()->toArray();
    }

    private function aggregateByArea(array $byBranch): array
    {
        $areaMap = [];
        foreach ($byBranch as $b) {
            $a = $b['area_name'];
            $areaMap[$a]['name'] = $a;
            $areaMap[$a]['omzet'] = ($areaMap[$a]['omzet'] ?? 0) + $b['omzet'];
            $areaMap[$a]['sosialisasi'] = ($areaMap[$a]['sosialisasi'] ?? 0) + $b['sosialisasi'];
            $areaMap[$a]['member'] = ($areaMap[$a]['member'] ?? 0) + $b['member'];
            $areaMap[$a]['prospective'] = ($areaMap[$a]['prospective'] ?? 0) + $b['prospective'];
            $areaMap[$a]['branch_count'] = ($areaMap[$a]['branch_count'] ?? 0) + 1;
        }

        return collect($areaMap)->sortByDesc('omzet')->values()->toArray();
    }

    private function periodLabel(string $pType, int $m, int $y, int $sem): string
    {
        if ($pType === 'yearly') {
            return 'Tahun '.$y;
        }
        if ($pType === 'semester') {
            return 'Semester '.$sem.' '.$y;
        }

        return Carbon::create($y, $m, 1)->translatedFormat('F Y');
    }

    private function emptyResult(int $m, int $y, string $pType, int $sem, ?int $branchId, ?int $areaId): array
    {
        $areas = Area::orderBy('name')->get(['id', 'name']);
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get(['id', 'branch_name']);

        return [
            'period' => $this->periodLabel($pType, $m, $y, $sem),
            'national' => ['omzet' => 0, 'sosialisasi' => 0, 'member' => 0, 'prospective' => 0],
            'by_area' => [],
            'by_branch' => [],
            'by_employee' => [],
            'areas' => $areas->toArray(),
            'branches' => $branches->toArray(),
            'filters' => ['branch_id' => $branchId, 'area_id' => $areaId, 'month' => $m, 'year' => $y, 'period_type' => $pType, 'semester' => $sem],
        ];
    }

    public function getExportData(array $filters = []): array
    {
        return array_map(fn ($b) => [
            'cabang' => $b['name'],
            'area' => $b['area_name'],
            'omzet' => $b['omzet'],
            'sosialisasi' => $b['sosialisasi'],
            'member_baru' => $b['member'],
            'prospective_member' => $b['prospective'],
        ], $this->getData($filters)['by_branch']);
    }
}
