<?php

namespace App\Jobs;

use App\Services\TelegramStorageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncTelegramFilesJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private int $limit = 100)
    {
    }

    public function handle(TelegramStorageService $storage): void
    {
        try {
            $synced = $storage->syncFilesFromGroup($this->limit);

            Log::info('Telegram file sync completed', [
                'synced_count' => count($synced),
                'results' => $synced,
            ]);

            $storage->logToGroup(
                'INFO',
                'telegram',
                'sync_files',
                sprintf('Synced %d files from storage group', count($synced))
            );
        } catch (\Exception $e) {
            Log::error('Telegram file sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $storage->logToGroup(
                'ERROR',
                'telegram',
                'sync_files',
                'Sync failed: ' . $e->getMessage()
            );

            throw $e;
        }
    }
}
