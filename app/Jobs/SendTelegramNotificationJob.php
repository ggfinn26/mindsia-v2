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

class SendTelegramNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        private readonly NotificationLog $log,
        private readonly string $body,
    ) {}

    public function handle(NotificationLogRepository $logRepository): void
    {
        $chatId = $this->log->channel;
        $botToken = config('services.telegram.bot_token');

        if (! $chatId || $chatId === 'unknown') {
            $logRepository->markFailed($this->log, 'chat_id not set.');

            return;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $this->body,
                'parse_mode' => 'HTML',
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Telegram API error: '.$response->body());
            }

            $logRepository->markSent($this->log);
        } catch (\Throwable $e) {
            $logRepository->markFailed($this->log, $e->getMessage());

            throw $e;
        }
    }
}
