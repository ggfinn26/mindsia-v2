<?php

namespace App\Http\Controllers\Admin\Bonus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bonus\StoreKpiBonusRuleRequest;
use App\Http\Requests\Bonus\StoreKpiBonusRuleTierRequest;
use App\Http\Requests\Bonus\UpdateKpiBonusRuleRequest;
use App\Http\Requests\Bonus\UpdateKpiBonusRuleTierRequest;
use App\Models\KpiBonusRule;
use App\Repositories\Bonus\KpiBonusRuleRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KpiBonusRuleController extends Controller
{
    public function __construct(
        private readonly KpiBonusRuleRepository $repo,
    ) {}

    public function index(): View
    {
        return view('admin.bonus.kpi-rules.index', [
            'rules' => $this->repo->all(),
        ]);
    }

    public function store(StoreKpiBonusRuleRequest $request): RedirectResponse
    {
        $rule = $this->repo->store($request->validated());

        return redirect()->route('bonus.kpi-rules.show', $rule)->with('success', 'Rule bonus KPI berhasil ditambahkan.');
    }

    public function show(KpiBonusRule $kpiRule): View
    {
        return view('admin.bonus.kpi-rules.show', [
            'rule' => $this->repo->findById($kpiRule->id),
        ]);
    }

    public function update(UpdateKpiBonusRuleRequest $request, KpiBonusRule $kpiRule): RedirectResponse
    {
        $this->repo->update($kpiRule, $request->validated());

        return redirect()->route('bonus.kpi-rules.show', $kpiRule)->with('success', 'Rule bonus KPI berhasil diperbarui.');
    }

    public function destroy(KpiBonusRule $kpiRule): RedirectResponse
    {
        $this->repo->delete($kpiRule);

        return redirect()->route('bonus.kpi-rules.index')->with('success', 'Rule bonus KPI berhasil dihapus.');
    }

    public function storeTier(StoreKpiBonusRuleTierRequest $request, KpiBonusRule $kpiRule): RedirectResponse
    {
        $this->repo->storeTier($kpiRule, $request->validated());

        return redirect()->route('bonus.kpi-rules.show', $kpiRule)->with('success', 'Tier berhasil ditambahkan.');
    }

    public function updateTier(UpdateKpiBonusRuleTierRequest $request, KpiBonusRule $kpiRule, int $tier): RedirectResponse
    {
        $this->repo->updateTier($tier, $request->validated());

        return redirect()->route('bonus.kpi-rules.show', $kpiRule)->with('success', 'Tier berhasil diperbarui.');
    }

    public function destroyTier(KpiBonusRule $kpiRule, int $tier): RedirectResponse
    {
        $this->repo->deleteTier($tier);

        return redirect()->route('bonus.kpi-rules.show', $kpiRule)->with('success', 'Tier berhasil dihapus.');
    }
}
