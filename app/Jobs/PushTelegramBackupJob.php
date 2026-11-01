<?php

namespace App\Jobs;

use App\Models\TelegramFile;
use App\Services\TelegramStorageService;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class PushTelegramBackupJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(
        public int $telegramFileId,
        public string $storagePath,
        public string $originalFilename,
        public ?string $caption = null,
    ) {
        $this->onQueue('telegram-backup');
    }

    /**
     * Antrean penuh dilepas tiap menit (release menambah attempts), jadi batasi pakai waktu, bukan jumlah percobaan.
     */
    public function retryUntil(): \DateTimeInterface
    {
        return now()->addHours(6);
    }

    public function uniqueId(): string
    {
        return (string) $this->telegramFileId;
    }

    public function middleware(): array
    {
        return [
            (new ThrottlesExceptions(20, 60))->backoff(5),
        ];
    }

    public function handle(TelegramStorageService $storage): void
    {
        $record = TelegramFile::find($this->telegramFileId);

        if (! $record || $record->isUploaded()) {
            return;
        }

        if (! $this->resolveLocalPath()) {
            $record->markFailed('Local file not found: '.$this->storagePath);
            Log::error('PushTelegramBackup: local file missing', ['path' => $this->storagePath]);

            return;
        }

        // Rate limit: max 20 messages per minute to same group (cache store, tanpa Redis)
        $allowed = RateLimiter::attempt(
            'telegram-backup',
            20,
            fn () => $this->uploadToTelegram($record, $storage),
            60,
        );

        if (! $allowed) {
            $this->release(60);
        }
    }

    /**
     * Cari file di disk local, disk public (tempat foto disimpan), lalu public_path.
     */
    private function resolveLocalPath(): ?string
    {
        foreach ([Storage::path($this->storagePath), Storage::disk('public')->path($this->storagePath), public_path($this->storagePath)] as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    protected function uploadToTelegram(TelegramFile $record, TelegramStorageService $storage): void
    {
        try {
            $localPath = $this->resolveLocalPath() ?? Storage::path($this->storagePath);

            if (! file_exists($localPath)) {
                $localPath = public_path($this->storagePath);
            }

            $botToken = config('services.telegram.bot_token');
            $chatId = config('services.telegram.chat_id');

            if (! $botToken || ! $chatId) {
                $record->markFailed('TELEGRAM_BOT_TOKEN or GROUP_STORAGE not configured');

                return;
            }

            $mimeType = $record->mime_type ?? mime_content_type($localPath) ?: 'application/octet-stream';
            $isPhoto = str_starts_with($mimeType, 'image/');

            if ($isPhoto) {
                $response = Http::attach('photo', fopen($localPath, 'r'), $this->originalFilename)
                    ->post("https://api.telegram.org/bot{$botToken}/sendPhoto", [
                        'chat_id' => $chatId,
                        'caption' => $this->caption,
                    ]);

                if ($response->successful()) {
                    $photos = $response->json('result.photo', []);
                    $fileId = ! empty($photos) ? end($photos)['file_id'] : null;
                }
            } else {
                $response = Http::attach('document', fopen($localPath, 'r'), $this->originalFilename)
                    ->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
                        'chat_id' => $chatId,
                        'caption' => $this->caption,
                    ]);

                if ($response->successful()) {
                    $fileId = $response->json('result.document.file_id');
                }
            }

            if ($response->successful() && isset($fileId)) {
                $record->update([
                    'telegram_file_id' => $fileId,
                    'backup_status' => 'uploaded',
                    'last_attempted_at' => now(),
                ]);
                Log::info('PushTelegramBackup: uploaded', ['file' => $this->originalFilename]);
            } else {
                $errorBody = $response->body();
                $record->markFailed("Telegram API error: {$errorBody}");
                Log::error('PushTelegramBackup: Telegram API error', ['response' => $errorBody]);
            }
        } catch (\Throwable $e) {
            $record->markFailed($e->getMessage());
            Log::error('PushTelegramBackup: exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
