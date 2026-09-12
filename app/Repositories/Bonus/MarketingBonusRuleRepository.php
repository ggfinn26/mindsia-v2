<?php

namespace App\Repositories\Bonus;

use App\Models\MarketingBonusRule;
use App\Models\MarketingBonusRuleTier;
use Illuminate\Database\Eloquent\Collection;

class MarketingBonusRuleRepository
{
    public function all(): Collection
    {
        return MarketingBonusRule::with('tiers')->get();
    }

    public function findById(int $id): MarketingBonusRule
    {
        return MarketingBonusRule::with('tiers')->findOrFail($id);
    }

    public function findActiveForEmployee(int $employeeId, int $positionId, int $roleId): Collection
    {
        return MarketingBonusRule::with('tiers')->forEmployee($employeeId, $positionId, $roleId)->get();
    }

    public function store(array $data): MarketingBonusRule
    {
        return MarketingBonusRule::create($data);
    }

    public function update(MarketingBonusRule $rule, array $data): MarketingBonusRule
    {
        $rule->update($data);

        return $rule->fresh('tiers');
    }

    public function storeTier(MarketingBonusRule $rule, array $data): MarketingBonusRuleTier
    {
        return $rule->tiers()->create($data);
    }

    public function updateTier(int $tierId, array $data): MarketingBonusRuleTier
    {
        $tier = MarketingBonusRuleTier::findOrFail($tierId);
        $tier->update($data);

        return $tier;
    }

    public function deleteTier(int $tierId): void
    {
        MarketingBonusRuleTier::destroy($tierId);
    }

    public function delete(MarketingBonusRule $rule): void
    {
        $rule->delete();
    }
}
