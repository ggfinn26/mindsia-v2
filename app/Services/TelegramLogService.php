<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramLogService
{
    private string $botToken;

    private int $groupLogId;

    private const API_BASE = 'https://api.telegram.org';

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->groupLogId = (int) env('GROUP_LOG');
    }

    public function logCreated(string $model, array $data, int $id): void
    {
        $message = $this->formatMessage('CREATE', $model, $id, $data);
        $this->send($message);
    }

    public function logUpdated(string $model, array $changes, int $id): void
    {
        $message = $this->formatMessage('UPDATE', $model, $id, $changes);
        $this->send($message);
    }

    public function logDeleted(string $model, int $id, array $data = []): void
    {
        $message = $this->formatMessage('DELETE', $model, $id, $data);
        $this->send($message);
    }

    private function formatMessage(string $action, string $model, int $id, array $data): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $actor = auth()->user()?->name ?? 'System';

        $dataStr = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "🔍 *Audit Log*\n".
            "━━━━━━━━━━━━━━━━━━━━\n".
            "*Action:* {$action}\n".
            "*Model:* {$model} (ID: {$id})\n".
            "*Actor:* {$actor}\n".
            "*Time:* {$timestamp}\n".
            "*Data:*\n```json\n{$dataStr}\n```";
    }

    private function send(string $message): void
    {
        try {
            Http::post("{$this->apiUrl()}/sendMessage", [
                'chat_id' => $this->groupLogId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error('TelegramLogService failed: '.$e->getMessage());
        }
    }

    private function apiUrl(): string
    {
        return self::API_BASE."/bot{$this->botToken}";
    }
}
