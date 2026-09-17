<?php

namespace App\Http\Controllers\Admin\Bonus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bonus\StoreSpecialBonusRuleConditionRequest;
use App\Http\Requests\Bonus\StoreSpecialBonusRuleRequest;
use App\Http\Requests\Bonus\UpdateSpecialBonusRuleConditionRequest;
use App\Http\Requests\Bonus\UpdateSpecialBonusRuleRequest;
use App\Models\SpecialBonusRule;
use App\Models\SpecialBonusRuleCondition;
use App\Repositories\Bonus\SpecialBonusRuleRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SpecialBonusRuleController extends Controller
{
    public function __construct(
        private readonly SpecialBonusRuleRepository $repo,
    ) {}

    public function index(): View
    {
        abort_unless(auth()->user()->can('bonus.special-rule.view'), 403);

        return view('admin.bonus.special-rules.index', [
            'rules' => $this->repo->all(),
        ]);
    }

    public function store(StoreSpecialBonusRuleRequest $request): RedirectResponse
    {
        $rule = $this->repo->store($request->validated());

        return redirect()->route('bonus.special-rules.show', $rule)->with('success', 'Rule bonus spesial berhasil ditambahkan.');
    }

    public function show(SpecialBonusRule $specialRule): View
    {
        abort_unless(auth()->user()->can('bonus.special-rule.view'), 403);

        return view('admin.bonus.special-rules.show', [
            'rule' => $this->repo->findById($specialRule->id),
        ]);
    }

    public function update(UpdateSpecialBonusRuleRequest $request, SpecialBonusRule $specialRule): RedirectResponse
    {
        $this->repo->update($specialRule, $request->validated());

        return redirect()->route('bonus.special-rules.show', $specialRule)->with('success', 'Rule bonus spesial berhasil diperbarui.');
    }

    public function destroy(SpecialBonusRule $specialRule): RedirectResponse
    {
        abort_unless(auth()->user()->can('bonus.special-rule.delete'), 403);
        $this->repo->delete($specialRule);

        return redirect()->route('bonus.special-rules.index')->with('success', 'Rule bonus spesial berhasil dihapus.');
    }

    public function toggleActive(SpecialBonusRule $specialRule): RedirectResponse
    {
        abort_unless(auth()->user()->can('bonus.special-rule.update'), 403);
        $specialRule->update(['is_active' => ! $specialRule->is_active]);

        return back()->with('success', 'Status bonus rule berhasil diperbarui.');
    }

    public function storeCondition(StoreSpecialBonusRuleConditionRequest $request, SpecialBonusRule $specialRule): RedirectResponse
    {
        $this->repo->storeCondition($specialRule, $request->validated());

        return redirect()->route('bonus.special-rules.show', $specialRule)->with('success', 'Kondisi berhasil ditambahkan.');
    }

    public function updateCondition(UpdateSpecialBonusRuleConditionRequest $request, SpecialBonusRule $specialRule, int $condition): RedirectResponse
    {
        abort_unless(SpecialBonusRuleCondition::where('id', $condition)->where('special_bonus_rule_id', $specialRule->id)->exists(), 403);
        $this->repo->updateCondition($condition, $request->validated());

        return redirect()->route('bonus.special-rules.show', $specialRule)->with('success', 'Kondisi berhasil diperbarui.');
    }

    public function destroyCondition(SpecialBonusRule $specialRule, int $condition): RedirectResponse
    {
        abort_unless(SpecialBonusRuleCondition::where('id', $condition)->where('special_bonus_rule_id', $specialRule->id)->exists(), 403);
        $this->repo->deleteCondition($condition);

        return redirect()->route('bonus.special-rules.show', $specialRule)->with('success', 'Kondisi berhasil dihapus.');
    }
}
