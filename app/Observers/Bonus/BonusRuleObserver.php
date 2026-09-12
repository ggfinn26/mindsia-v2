<?php

namespace App\Observers\Bonus;

use App\Repositories\Bonus\BonusRuleChangeHistoryRepository;
use Illuminate\Database\Eloquent\Model;

class BonusRuleObserver
{
    public function __construct(
        private readonly BonusRuleChangeHistoryRepository $historyRepo,
        private readonly string $ruleType,
    ) {}

    public function created(Model $rule): void
    {
        $this->historyRepo->store($this->ruleType, $rule->id, 'created', null, $rule->toArray());
    }

    public function updated(Model $rule): void
    {
        $action = $rule->isDirty('is_active') && ! $rule->is_active ? 'deactivated' : 'updated';
        $this->historyRepo->store($this->ruleType, $rule->id, $action, $rule->getOriginal(), $rule->toArray());
    }

    public function deleted(Model $rule): void
    {
        $this->historyRepo->store($this->ruleType, $rule->id, 'deleted', $rule->toArray(), null);
    }
}
