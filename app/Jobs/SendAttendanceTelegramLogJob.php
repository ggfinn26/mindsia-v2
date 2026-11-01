<?php

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Repositories\Notification\NotificationLogRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Mengirim pesan log absen (beserta foto selfie bila ada) ke grup Telegram pusat.
 * Teks dan foto dibaca dari payload NotificationLog, jadi kirim ulang dari UI memakai isi yang sama.
 */
class SendAttendanceTelegramLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300];

    public function __construct(public readonly NotificationLog $log) {}

    public function handle(NotificationLogRepository $logRepository): void
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = $this->log->channel;
        $body = (string) ($this->log->payload['body'] ?? '');

        if (! $botToken || ! $chatId || $chatId === 'unknown') {
            $logRepository->markFailed($this->log, 'TELEGRAM_BOT_TOKEN atau TELEGRAM_ATTENDANCE_CHAT_ID belum diatur.');

            return;
        }

        try {
            $photo = $this->photoFile();
            $response = $photo
                ? Http::attach('photo', fopen($photo, 'r'), 'selfie.jpg')->post("https://api.telegram.org/bot{$botToken}/sendPhoto", ['chat_id' => $chatId, 'caption' => mb_substr($body, 0, 1024)])
                : Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", ['chat_id' => $chatId, 'text' => $body]);

            if (! $response->successful()) {
                throw new \RuntimeException('Telegram API error: '.$response->body());
            }

            $logRepository->markSent($this->log);
        } catch (\Throwable $e) {
            $logRepository->markFailed($this->log, $e->getMessage());

            throw $e;
        }
    }

    private function photoFile(): ?string
    {
        $path = $this->log->payload['photo_path'] ?? null;
        if (! $path) {
            return null;
        }

        foreach ([Storage::path($path), public_path($path)] as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
