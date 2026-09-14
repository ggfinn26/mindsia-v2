<?php

namespace App\Observers\Bonus;

use App\Repositories\Bonus\BonusRuleChangeHistoryRepository;
use Illuminate\Database\Eloquent\Model;

class BonusChildObserver
{
    public function __construct(
        private readonly BonusRuleChangeHistoryRepository $historyRepo,
        private readonly string $ruleType,
        private readonly string $ruleIdKey,
        private readonly string $childLabel, // 'tier' | 'condition'
    ) {}

    public function created(Model $child): void
    {
        $this->historyRepo->store($this->ruleType, $child->{$this->ruleIdKey}, "{$this->childLabel}_added", null, $child->toArray());
    }

    public function updated(Model $child): void
    {
        $action = $child->isDirty('is_active') && ! $child->is_active
            ? "{$this->childLabel}_deactivated"
            : "{$this->childLabel}_updated";

        $this->historyRepo->store($this->ruleType, $child->{$this->ruleIdKey}, $action, $child->getOriginal(), $child->toArray());
    }

    public function deleted(Model $child): void
    {
        $this->historyRepo->store($this->ruleType, $child->{$this->ruleIdKey}, "{$this->childLabel}_deleted", $child->toArray(), null);
    }
}
