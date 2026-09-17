<?php

namespace App\Repositories\Bonus;

use App\Models\BonusRuleChangeHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BonusRuleChangeHistoryRepository
{
    public function store(
        string $bonusType,
        int $ruleId,
        string $action,
        ?array $oldValues,
        ?array $newValues,
        ?int $changedByEmployeeId = null,
    ): void {
        $employeeId = $changedByEmployeeId ?? auth('web')->user()?->employee?->id;

        if ($employeeId === null) {
            throw new \RuntimeException('User yang melakukan perubahan bonus rule harus memiliki employee record.');
        }

        BonusRuleChangeHistory::create([
            'bonus_type' => $bonusType,
            'rule_id' => $ruleId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_by_employee_id' => $employeeId,
        ]);
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return BonusRuleChangeHistory::with('changedBy')
            ->when($filters['bonus_type'] ?? null, fn ($q, $v) => $q->where('bonus_type', $v))
            ->when($filters['rule_id'] ?? null, fn ($q, $v) => $q->where('rule_id', $v))
            ->orderByDesc('created_at')
            ->paginate(25);
    }
}
