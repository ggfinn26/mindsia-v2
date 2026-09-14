<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\StoreMarketingKpiRankingRuleRequest;
use App\Models\Employee;
use App\Models\MarketingKpiRankingRule;
use App\Repositories\Marketing\MarketingKpiRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingKpiController extends Controller
{
    public function __construct(
        private readonly MarketingKpiRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('marketing.kpi.view'), 403);

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $snapshots = $this->repository->latestByPeriod($month, $year);

        return view('marketing.kpi.index', compact('snapshots', 'month', 'year'));
    }

    public function show(Employee $employee, Request $request): View
    {
        abort_unless(
            $request->user()->can('marketing.kpi.view') || $request->user()->employee->id === $employee->id,
            403
        );

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $history = $this->repository->historyByEmployee($employee->id, $month, $year);

        return view('marketing.kpi.show', compact('history', 'employee', 'month', 'year'));
    }

    // Ranking rule management
    public function rules(Request $request): View
    {
        abort_unless($request->user()->can('marketing.ranking_rule.manage'), 403);

        $rules = MarketingKpiRankingRule::with('criteria')->latest()->get();

        return view('marketing.kpi.rules', compact('rules'));
    }

    public function storeRule(StoreMarketingKpiRankingRuleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        MarketingKpiRankingRule::create(array_merge($validated, [
            'is_active' => false,
            'created_by_employee_id' => $request->user()->employee->id,
        ]));

        return back()->with('success', 'Rule berhasil dibuat.');
    }

    public function activateRule(Request $request, MarketingKpiRankingRule $rule): RedirectResponse
    {
        abort_unless($request->user()->can('marketing.ranking_rule.manage'), 403);

        // single active — deactivate others first
        MarketingKpiRankingRule::where('id', '!=', $rule->id)->update(['is_active' => false]);
        $rule->update(['is_active' => true]);

        return back()->with('success', "Rule '{$rule->rule_name}' diaktifkan.");
    }
}
