<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\StoreKpiGradeRuleRequest;
use App\Http\Requests\Kpi\UpdateKpiGradeRuleRequest;
use App\Models\KpiGradeRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class KpiGradeRuleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            (new Middleware('can:kpi.grade_rule.view'))->only(['index', 'create', 'edit']),
            (new Middleware('can:kpi.grade_rule.delete'))->only(['destroy']),
        ];
    }

    public function index(): View
    {
        return view('kpi.grade-rules.index', [
            'rules' => KpiGradeRule::orderBy('minimum_score')->get(),
        ]);
    }

    public function create(): View
    {
        return view('kpi.grade-rules.create');
    }

    public function store(StoreKpiGradeRuleRequest $request): RedirectResponse
    {
        KpiGradeRule::create($request->validated());

        return redirect()->route('kpi.grade-rules.index')->with('success', 'Grade rule berhasil ditambahkan.');
    }

    public function edit(KpiGradeRule $kpiGradeRule): View
    {
        return view('kpi.grade-rules.edit', ['rule' => $kpiGradeRule]);
    }

    public function update(UpdateKpiGradeRuleRequest $request, KpiGradeRule $kpiGradeRule): RedirectResponse
    {
        $kpiGradeRule->update($request->validated());

        return redirect()->route('kpi.grade-rules.index')->with('success', 'Grade rule berhasil diperbarui.');
    }

    public function destroy(KpiGradeRule $kpiGradeRule): RedirectResponse
    {
        $kpiGradeRule->delete();

        return redirect()->route('kpi.grade-rules.index')->with('success', 'Grade rule berhasil dihapus.');
    }
}
