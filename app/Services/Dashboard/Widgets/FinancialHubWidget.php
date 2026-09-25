<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Area;
use App\Models\Branch;
use App\Models\BranchMonthlyCost;
use App\Models\EmployeePayroll;
use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialHubWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'financial_hub';
    }

    public function getData(array $filters = []): array
    {
        $currentMonth = (int) ($filters['month'] ?? now()->month);
        $currentYear = (int) ($filters['year'] ?? now()->year);
        $filterAreaId = $filters['area_id'] ?? null;
        $now = Carbon::create($currentYear, $currentMonth, 1);
        $prev = $now->copy()->subMonth();

        $branches = Branch::with('area')
            ->where('is_active', true)
            ->when($filterAreaId, fn ($q) => $q->where('areas_id', $filterAreaId))
            ->get();

        $branchIds = $branches->pluck('id');

        // Bulk-load revenue, payroll, operational to avoid N+1
        // branch_id lives on MemberData, not MemberRegistration → use nested relationship
        $revenueByBranch = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->whereHas('registration.memberData', fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->with(['registration:id,members_data_id', 'registration.memberData:id,branch_id'])
            ->get(['id', 'member_registration_id', 'amount'])
            ->groupBy(fn ($p) => $p->registration?->memberData?->branch_id)
            ->map(fn ($ps) => $ps->sum('amount'));

        $payrollByBranch = EmployeePayroll::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->whereHas('employee', fn ($e) => $e->whereIn('branch_id', $branchIds))
            ->with('employee:id,branch_id')
            ->get(['id', 'net_amount'])
            ->groupBy(fn ($p) => $p->employee?->branch_id)
            ->map(fn ($ps) => $ps->sum('net_amount'));

        $opByBranch = BranchMonthlyCost::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->whereIn('branch_id', $branchIds)
            ->get(['branch_id', 'amount'])
            ->groupBy('branch_id')
            ->map(fn ($rows) => $rows->sum('amount'));

        // Prev month revenue per branch
        $prevRevenueByBranch = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prev->month)
            ->whereYear('paid_at', $prev->year)
            ->whereHas('registration.memberData', fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->with(['registration:id,members_data_id', 'registration.memberData:id,branch_id'])
            ->get(['id', 'member_registration_id', 'amount'])
            ->groupBy(fn ($p) => $p->registration?->memberData?->branch_id)
            ->map(fn ($ps) => $ps->sum('amount'));

        $adminFeeByBranch = $this->resolveAdminFeeByBranch($branchIds, $currentMonth, $currentYear);

        $branchData = $branches->map(function ($branch) use ($revenueByBranch, $payrollByBranch, $opByBranch, $prevRevenueByBranch, $adminFeeByBranch) {
            $grossRevenue = $revenueByBranch[$branch->id] ?? 0;
            $adminFee = $adminFeeByBranch[$branch->id] ?? 0;
            $revenue = max(0, $grossRevenue - $adminFee);
            $payroll = $payrollByBranch[$branch->id] ?? 0;
            $operational = $opByBranch[$branch->id] ?? 0;
            $profit = $revenue - $payroll - $operational;
            $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;
            $prevRevenue = $prevRevenueByBranch[$branch->id] ?? 0;
            $trend = $prevRevenue > 0 ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;

            $words = preg_split('/\s+/', trim($branch->branch_name));
            $abbr = strtoupper(implode('', array_map(fn ($w) => $w[0] ?? '', array_slice($words, 0, 3))));

            return [
                'id' => $branch->id,
                'name' => $branch->branch_name,
                'abbr' => substr($abbr, 0, 3),
                'area_name' => $branch->area?->name ?? '-',
                'revenue' => $revenue,
                'profit' => $profit,
                'margin' => $margin,
                'trend' => $trend,
            ];
        })->sortByDesc('revenue')->values();

        $nationalRevenue = $branchData->sum('revenue');
        $nationalProfit = $branchData->sum('profit');
        $branchCount = $branchData->count();
        $nationalMargin = $nationalRevenue > 0 ? round(($nationalProfit / $nationalRevenue) * 100, 1) : 0;
        $avgBranchRevenue = $branchCount > 0 ? intdiv($nationalRevenue, $branchCount) : 0;

        // National prev-month trend
        $prevNatGrossRevenue = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prev->month)
            ->whereYear('paid_at', $prev->year)
            ->whereHas('registration.memberData', fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('amount');
        $prevNatAdminFee = $this->resolveAdminFeeByBranch($branchIds, $prev->month, $prev->year)->sum();
        $prevNatRevenue = max(0, $prevNatGrossRevenue - $prevNatAdminFee);
        $prevNatPayroll = EmployeePayroll::where('payment_status', 'paid')
            ->whereMonth('paid_at', $prev->month)
            ->whereYear('paid_at', $prev->year)
            ->whereHas('employee', fn ($e) => $e->whereIn('branch_id', $branchIds))
            ->sum('net_amount');
        $prevNatOp = BranchMonthlyCost::where('period_month', $prev->month)
            ->where('period_year', $prev->year)
            ->whereIn('branch_id', $branchIds)
            ->sum('amount');

        $nationalTrend = $prevNatRevenue > 0
            ? round((($nationalRevenue - $prevNatRevenue) / $prevNatRevenue) * 100, 1)
            : 0;
        $prevNatProfit = $prevNatRevenue - $prevNatPayroll - $prevNatOp;
        $profitTrend = $prevNatProfit != 0
            ? round((($nationalProfit - $prevNatProfit) / abs($prevNatProfit)) * 100, 1)
            : 0;

        // Status classification
        $superiorCount = $branchData->filter(fn ($b) => $b['margin'] >= 25)->count();
        $evaluasiCount = $branchData->filter(fn ($b) => $b['margin'] < 25)->count();

        // Annotate each branch with vs_national & status
        $maxRevenue = $branchData->max('revenue') ?: 1;
        $maxBenchmarkLine = $avgBranchRevenue > 0 ? min(100, round(($avgBranchRevenue / $maxRevenue) * 100)) : 50;

        $annotated = $branchData->map(function ($b) use ($avgBranchRevenue) {
            $vsAvg = $avgBranchRevenue > 0
                ? round((($b['revenue'] - $avgBranchRevenue) / $avgBranchRevenue) * 100, 1)
                : 0;
            $status = match (true) {
                $b['margin'] >= 30 => 'Top Tier',
                $b['margin'] >= 25 => 'On Track',
                $b['margin'] >= 20 => 'Baseline',
                default => 'Need Attention',
            };

            return array_merge($b, ['vs_national' => $vsAvg, 'status' => $status]);
        })->values()->toArray();

        // Area contributions (donut chart)
        $areaContributions = [];
        if (! $filterAreaId) {
            $areas = Area::with('branches:id,areas_id')->get();
            $offset = 0;
            $colors = ['#059669', '#2563eb', '#f59e0b', '#94a3b8'];
            foreach ($areas as $i => $area) {
                $areaIds = $area->branches->pluck('id')->intersect($branchIds);
                $areaRevenue = collect($annotated)->whereIn('id', $areaIds->toArray())->sum('revenue');
                $pct = $nationalRevenue > 0 ? round(($areaRevenue / $nationalRevenue) * 100) : 0;
                if ($pct > 0) {
                    $areaContributions[] = [
                        'name' => $area->name,
                        'revenue' => $areaRevenue,
                        'percentage' => $pct,
                        'offset' => -$offset,
                        'color' => $colors[$i % count($colors)],
                    ];
                    $offset += $pct;
                }
            }
        }

        return [
            'period' => $now->translatedFormat('F Y'),
            'national_revenue' => $nationalRevenue,
            'national_profit' => $nationalProfit,
            'national_margin' => $nationalMargin,
            'national_trend' => $nationalTrend,
            'profit_trend' => $profitTrend,
            'avg_branch_revenue' => $avgBranchRevenue,
            'branch_count' => $branchCount,
            'superior_count' => $superiorCount,
            'evaluasi_count' => $evaluasiCount,
            'branches' => $annotated,
            'top_branches' => array_slice($annotated, 0, 4),
            'area_contributions' => $areaContributions,
            'max_revenue' => $maxRevenue,
            'benchmark_pct' => $maxBenchmarkLine,
        ];
    }

    /**
     * Admin fee per branch (gross revenue deduction).
     * Mirrors MarketingSnapshotService::resolveAdminFeeDeducted but grouped by branch.
     *
     * @param  Collection<int, int>  $branchIds
     * @return Collection<int, float>
     */
    private function resolveAdminFeeByBranch(Collection $branchIds, int $month, int $year): Collection
    {
        $ids = $branchIds->toArray();
        $totals = collect($ids)->mapWithKeys(fn ($id) => [$id => 0.0]);

        // per_registration + on_registration — counted at registration date
        $rows = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->whereIn('md.branch_id', $ids)
            ->whereYear('mr.created_at', $year)
            ->whereMonth('mr.created_at', $month)
            ->where('p.admin_fee_mode', 'per_registration')
            ->where('p.admin_fee_timing', 'on_registration')
            ->groupBy('md.branch_id')
            ->selectRaw('md.branch_id, SUM(p.admin_fee) as total')
            ->pluck('total', 'branch_id');

        foreach ($rows as $branchId => $total) {
            $totals[$branchId] = ($totals[$branchId] ?? 0.0) + (float) $total;
        }

        // per_registration + on_first_payment — counted at first payment date
        $rows = DB::table('members_registration as mr')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->joinSub(
                DB::table('member_payments')
                    ->selectRaw('member_registration_id, MIN(paid_at) as first_paid')
                    ->where('payment_status', 'paid')
                    ->groupBy('member_registration_id'),
                'fp',
                'fp.member_registration_id', '=', 'mr.id'
            )
            ->whereIn('md.branch_id', $ids)
            ->whereYear('fp.first_paid', $year)
            ->whereMonth('fp.first_paid', $month)
            ->where('p.admin_fee_mode', 'per_registration')
            ->where('p.admin_fee_timing', 'on_first_payment')
            ->groupBy('md.branch_id')
            ->selectRaw('md.branch_id, SUM(p.admin_fee) as total')
            ->pluck('total', 'branch_id');

        foreach ($rows as $branchId => $total) {
            $totals[$branchId] = ($totals[$branchId] ?? 0.0) + (float) $total;
        }

        // per_transaction — counted per paid payment
        $rows = DB::table('member_payments as mp')
            ->join('members_registration as mr', 'mp.member_registration_id', '=', 'mr.id')
            ->join('members_data as md', 'mr.members_data_id', '=', 'md.id')
            ->join('programs as p', 'mr.program_id', '=', 'p.id')
            ->whereIn('md.branch_id', $ids)
            ->whereYear('mp.paid_at', $year)
            ->whereMonth('mp.paid_at', $month)
            ->where('mp.payment_status', 'paid')
            ->where('p.admin_fee_mode', 'per_transaction')
            ->groupBy('md.branch_id')
            ->selectRaw('md.branch_id, SUM(p.admin_fee) as total')
            ->pluck('total', 'branch_id');

        foreach ($rows as $branchId => $total) {
            $totals[$branchId] = ($totals[$branchId] ?? 0.0) + (float) $total;
        }

        return $totals;
    }

    public function getExportData(array $filters = []): array
    {
        $data = $this->getData($filters);

        return array_map(fn ($b) => [
            'cabang' => $b['name'],
            'area' => $b['area_name'],
            'revenue' => $b['revenue'],
            'profit' => $b['profit'],
            'margin' => $b['margin'],
            'trend' => $b['trend'],
            'status' => $b['status'],
        ], $data['branches']);
    }
}
