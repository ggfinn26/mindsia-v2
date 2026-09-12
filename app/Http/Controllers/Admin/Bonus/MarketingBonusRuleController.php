<?php

namespace App\Http\Controllers\Admin\Bonus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bonus\StoreMarketingBonusRuleRequest;
use App\Http\Requests\Bonus\StoreMarketingBonusRuleTierRequest;
use App\Http\Requests\Bonus\UpdateMarketingBonusRuleRequest;
use App\Http\Requests\Bonus\UpdateMarketingBonusRuleTierRequest;
use App\Models\MarketingBonusRule;
use App\Repositories\Bonus\MarketingBonusRuleRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MarketingBonusRuleController extends Controller
{
    public function __construct(
        private readonly MarketingBonusRuleRepository $repo,
    ) {}

    public function index(): View
    {
        return view('admin.bonus.marketing-rules.index', [
            'rules' => $this->repo->all(),
        ]);
    }

    public function store(StoreMarketingBonusRuleRequest $request): RedirectResponse
    {
        $rule = $this->repo->store($request->validated());

        return redirect()->route('bonus.marketing-rules.show', $rule)->with('success', 'Rule bonus marketing berhasil ditambahkan.');
    }

    public function show(MarketingBonusRule $marketingRule): View
    {
        return view('admin.bonus.marketing-rules.show', [
            'rule' => $this->repo->findById($marketingRule->id),
        ]);
    }

    public function update(UpdateMarketingBonusRuleRequest $request, MarketingBonusRule $marketingRule): RedirectResponse
    {
        $this->repo->update($marketingRule, $request->validated());

        return redirect()->route('bonus.marketing-rules.show', $marketingRule)->with('success', 'Rule bonus marketing berhasil diperbarui.');
    }

    public function destroy(MarketingBonusRule $marketingRule): RedirectResponse
    {
        $this->repo->delete($marketingRule);

        return redirect()->route('bonus.marketing-rules.index')->with('success', 'Rule bonus marketing berhasil dihapus.');
    }

    public function storeTier(StoreMarketingBonusRuleTierRequest $request, MarketingBonusRule $marketingRule): RedirectResponse
    {
        $this->repo->storeTier($marketingRule, $request->validated());

        return redirect()->route('bonus.marketing-rules.show', $marketingRule)->with('success', 'Tier berhasil ditambahkan.');
    }

    public function updateTier(UpdateMarketingBonusRuleTierRequest $request, MarketingBonusRule $marketingRule, int $tier): RedirectResponse
    {
        $this->repo->updateTier($tier, $request->validated());

        return redirect()->route('bonus.marketing-rules.show', $marketingRule)->with('success', 'Tier berhasil diperbarui.');
    }

    public function destroyTier(MarketingBonusRule $marketingRule, int $tier): RedirectResponse
    {
        $this->repo->deleteTier($tier);

        return redirect()->route('bonus.marketing-rules.show', $marketingRule)->with('success', 'Tier berhasil dihapus.');
    }
}
