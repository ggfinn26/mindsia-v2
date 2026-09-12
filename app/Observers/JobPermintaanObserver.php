<?php

namespace App\Observers;

use App\Models\JobPermintaan;
use App\Services\TelegramLogService;

class JobPermintaanObserver
{
    public function __construct(
        private readonly TelegramLogService $telegramLogService
    ) {}

    public function created(JobPermintaan $permintaan): void
    {
        $this->telegramLogService->log(
            level: 'INFO',
            domain: 'recruitment',
            action: 'create_job_permintaan',
            detail: "Job Permintaan baru dibuat (ID: {$permintaan->id}) dari cabang {$permintaan->branch_id}"
        );
    }

    public function updated(JobPermintaan $permintaan): void
    {
        if ($permintaan->wasChanged('status')) {
            $this->telegramLogService->log(
                level: 'INFO',
                domain: 'recruitment',
                action: 'update_job_permintaan_status',
                detail: "Status Job Permintaan {$permintaan->id} berubah menjadi {$permintaan->status}"
            );
        }
    }
}
