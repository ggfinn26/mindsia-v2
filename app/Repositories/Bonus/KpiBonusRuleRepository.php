<?php

namespace App\Repositories\Bonus;

use App\Models\KpiBonusRule;
use App\Models\KpiBonusRuleTier;
use Illuminate\Database\Eloquent\Collection;

class KpiBonusRuleRepository
{
    public function all(): Collection
    {
        return KpiBonusRule::with('tiers')->get();
    }

    public function findById(int $id): KpiBonusRule
    {
        return KpiBonusRule::with('tiers')->findOrFail($id);
    }

    public function findActiveForEmployee(int $employeeId, int $positionId, int $roleId): Collection
    {
        return KpiBonusRule::with('tiers')->forEmployee($employeeId, $positionId, $roleId)->get();
    }

    public function store(array $data): KpiBonusRule
    {
        return KpiBonusRule::create($data);
    }

    public function update(KpiBonusRule $rule, array $data): KpiBonusRule
    {
        $rule->update($data);

        return $rule->fresh('tiers');
    }

    public function storeTier(KpiBonusRule $rule, array $data): KpiBonusRuleTier
    {
        return $rule->tiers()->create($data);
    }

    public function updateTier(int $tierId, array $data): KpiBonusRuleTier
    {
        $tier = KpiBonusRuleTier::findOrFail($tierId);
        $tier->update($data);

        return $tier;
    }

    public function deleteTier(int $tierId): void
    {
        KpiBonusRuleTier::destroy($tierId);
    }

    public function delete(KpiBonusRule $rule): void
    {
        $rule->delete();
    }
}
