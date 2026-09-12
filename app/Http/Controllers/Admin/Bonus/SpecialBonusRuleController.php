<?php

namespace App\Http\Controllers\Admin\Bonus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bonus\StoreSpecialBonusRuleConditionRequest;
use App\Http\Requests\Bonus\StoreSpecialBonusRuleRequest;
use App\Http\Requests\Bonus\UpdateSpecialBonusRuleConditionRequest;
use App\Http\Requests\Bonus\UpdateSpecialBonusRuleRequest;
use App\Models\SpecialBonusRule;
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
        $this->repo->delete($specialRule);

        return redirect()->route('bonus.special-rules.index')->with('success', 'Rule bonus spesial berhasil dihapus.');
    }

    public function storeCondition(StoreSpecialBonusRuleConditionRequest $request, SpecialBonusRule $specialRule): RedirectResponse
    {
        $this->repo->storeCondition($specialRule, $request->validated());

        return redirect()->route('bonus.special-rules.show', $specialRule)->with('success', 'Kondisi berhasil ditambahkan.');
    }

    public function updateCondition(UpdateSpecialBonusRuleConditionRequest $request, SpecialBonusRule $specialRule, int $condition): RedirectResponse
    {
        $this->repo->updateCondition($condition, $request->validated());

        return redirect()->route('bonus.special-rules.show', $specialRule)->with('success', 'Kondisi berhasil diperbarui.');
    }

    public function destroyCondition(SpecialBonusRule $specialRule, int $condition): RedirectResponse
    {
        $this->repo->deleteCondition($condition);

        return redirect()->route('bonus.special-rules.show', $specialRule)->with('success', 'Kondisi berhasil dihapus.');
    }
}
