<?php

namespace App\Console\Commands;

use App\Services\TelegramStorageService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('telegram:process-pending')]
#[Description('Process pending Telegram updates and log each file')]
class ProcessPendingTelegramUpdatesCommand extends Command
{
    public function handle(TelegramStorageService $storage): int
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $groupStorage = (int) env('GROUP_STORAGE');

        $this->info('Fetching pending updates...');

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/getUpdates", [
                'allowed_updates' => ['message'],
                'timeout' => 5,
            ]);

            $data = $response->json();

            if (!($data['ok'] ?? false)) {
                $this->error('Failed to fetch updates');
                return 1;
            }

            $updates = $data['result'] ?? [];

            if (empty($updates)) {
                $this->info('No pending updates');
                return 0;
            }

            $this->info('Found ' . count($updates) . ' updates');
            $this->newLine();

            $processed = 0;

            foreach ($updates as $update) {
                $message = $update['message'] ?? null;

                if (!$message || ($message['chat']['id'] ?? null) != $groupStorage) {
                    continue;
                }

                $filename = 'unknown';
                $fileId = null;
                $fileSize = null;

                // Handle documents
                if ($document = $message['document'] ?? null) {
                    $fileId = $document['file_id'];
                    $filename = $document['file_name'] ?? 'document';
                    $fileSize = $document['file_size'] ?? null;
                    $this->line('📄 Document: ' . $filename);
                }
                // Handle photos
                elseif ($photos = $message['photo'] ?? null) {
                    $photo = end($photos);
                    $fileId = $photo['file_id'];
                    $filename = 'photo_' . date('YmdHis') . '.jpg';
                    $fileSize = $photo['file_size'] ?? null;
                    $this->line('📸 Photo: ' . $filename);
                } else {
                    continue;
                }

                // Send log
                try {
                    $storage->logToGroup(
                        'INFO',
                        'pending_processor',
                        'file_processed',
                        "File: {$filename} | Size: {$fileSize} bytes | FileID: {$fileId}"
                    );

                    $this->line('   ✓ Logged to GROUP_LOG');
                    $processed++;
                } catch (\Exception $e) {
                    $this->error('   ✗ Log error: ' . $e->getMessage());
                }
            }

            $this->newLine();
            $this->info("✓ Processed {$processed} files");

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
