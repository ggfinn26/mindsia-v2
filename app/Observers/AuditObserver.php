<?php

namespace App\Observers;

use App\Services\TelegramLogService;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function __construct(
        private readonly TelegramLogService $telegramLog
    ) {}

    public function created(Model $model): void
    {
        $this->telegramLog->logCreated(
            class_basename($model),
            $model->getAttributes(),
            $model->id
        );
    }

    public function updated(Model $model): void
    {
        $this->telegramLog->logUpdated(
            class_basename($model),
            $model->getChanges(),
            $model->id
        );
    }

    public function deleted(Model $model): void
    {
        $this->telegramLog->logDeleted(
            class_basename($model),
            $model->id,
            $model->getAttributes()
        );
    }
}
