<?php

namespace App\Observers;

use App\Models\Survey;
use App\Services\TelegramLogService;

class SurveyObserver
{
    public function __construct(
        private readonly TelegramLogService $telegramLogService,
    ) {}

    public function created(Survey $survey): void
    {
        $this->telegramLogService->log('INFO', 'survey', 'create_survey', "Survey baru dibuat: {$survey->survey_name} (ID: {$survey->id})");
    }

    public function updated(Survey $survey): void
    {
        $this->telegramLogService->log('INFO', 'survey', 'update_survey', "Survey {$survey->id} diperbarui: {$survey->survey_name}");
    }

    public function deleted(Survey $survey): void
    {
        $this->telegramLogService->log('WARN', 'survey', 'delete_survey', "Survey dihapus: {$survey->survey_name} (ID: {$survey->id})");
    }
}
