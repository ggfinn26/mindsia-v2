<?php

namespace App\Observers;

use App\Models\ToeflTest;
use App\Services\TelegramLogService;

class ToeflTestObserver
{
    public function __construct(
        private readonly TelegramLogService $telegramLogService,
    ) {}

    public function created(ToeflTest $test): void
    {
        $this->telegramLogService->log('INFO', 'toefl', 'create_toefl_test', "Tes TOEFL baru dibuat: {$test->test_name} (ID: {$test->id})");
    }

    public function updated(ToeflTest $test): void
    {
        if ($test->wasChanged('status') && $test->status === ToeflTest::STATUS_PUBLISHED) {
            $this->telegramLogService->log('INFO', 'toefl', 'publish_toefl_test', "Tes TOEFL di-publish: {$test->test_name} (ID: {$test->id})");
        } else {
            $this->telegramLogService->log('INFO', 'toefl', 'update_toefl_test', "Tes TOEFL diperbarui: {$test->test_name}");
        }
    }
}
