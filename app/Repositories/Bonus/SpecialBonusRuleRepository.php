<?php

namespace App\Repositories\Bonus;

use App\Models\SpecialBonusRule;
use App\Models\SpecialBonusRuleCondition;
use Illuminate\Database\Eloquent\Collection;

class SpecialBonusRuleRepository
{
    public function all(): Collection
    {
        return SpecialBonusRule::with('conditions')->get();
    }

    public function findById(int $id): SpecialBonusRule
    {
        return SpecialBonusRule::with('conditions')->findOrFail($id);
    }

    public function findActiveForEmployee(int $employeeId, int $positionId, int $roleId): Collection
    {
        return SpecialBonusRule::with(['conditions' => fn ($q) => $q->where('is_active', true)])
            ->forEmployee($employeeId, $positionId, $roleId)
            ->get();
    }

    public function store(array $data): SpecialBonusRule
    {
        return SpecialBonusRule::create($data);
    }

    public function update(SpecialBonusRule $rule, array $data): SpecialBonusRule
    {
        $rule->update($data);

        return $rule->fresh('conditions');
    }

    public function storeCondition(SpecialBonusRule $rule, array $data): SpecialBonusRuleCondition
    {
        return $rule->conditions()->create($data);
    }

    public function updateCondition(int $conditionId, array $data): SpecialBonusRuleCondition
    {
        $condition = SpecialBonusRuleCondition::findOrFail($conditionId);
        $condition->update($data);

        return $condition;
    }

    public function deleteCondition(int $conditionId): void
    {
        SpecialBonusRuleCondition::destroy($conditionId);
    }

    public function delete(SpecialBonusRule $rule): void
    {
        $rule->delete();
    }
}
