<?php

namespace App\Console\Commands;

use App\Services\TelegramStorageService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('telegram:sync {--limit=100 : Number of messages to sync}')]
#[Description('Sync files from Telegram storage group (polling fallback)')]
class SyncTelegramFilesCommand extends Command
{
    public function handle(TelegramStorageService $storage): int
    {
        $limit = (int) $this->option('limit');

        $this->info('Syncing Telegram files...');
        $this->line('Limit: ' . $limit);
        $this->newLine();

        try {
            $startTime = microtime(true);

            $synced = $storage->syncFilesFromGroup($limit);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            if (empty($synced)) {
                $this->info('✓ Sync completed (no new files)');
                $this->line('Duration: ' . $duration . 'ms');
                return 0;
            }

            $this->info('✓ Sync completed');
            $this->newLine();
            $this->line('Summary:');
            $this->line('   Synced: ' . count($synced));
            $this->line('   Duration: ' . $duration . 'ms');
            $this->newLine();

            $this->line('Details:');
            foreach ($synced as $result) {
                if ($result['status'] === 'synced') {
                    $this->line('   ✓ ' . $result['filename'] . ' (ID: ' . $result['file_id'] . ')');
                } else {
                    $this->line('   - ' . $result['file_id'] . ' (skipped)');
                }
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Sync failed: ' . $e->getMessage());
            Log::error('Telegram sync error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
    }
}
