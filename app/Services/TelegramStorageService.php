<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TelegramStorageService
{
    private string $botToken;
    private int $groupStorageId;
    private int $groupLogId;
    private const API_BASE = 'https://api.telegram.org';

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->groupStorageId = (int) env('GROUP_STORAGE');
        $this->groupLogId = (int) env('GROUP_LOG');
    }

    public function uploadFile(
        string $filePath,
        string $originalFilename,
        ?string $entityType = null,
        ?int $entityId = null
    ): array {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: $filePath");
        }

        $mimeType = mime_content_type($filePath);
        $fileSize = filesize($filePath);

        $response = Http::attach(
            'document',
            fopen($filePath, 'r'),
            $originalFilename
        )->post("{$this->apiUrl()}/sendDocument", [
            'chat_id' => $this->groupStorageId,
            'caption' => "File: {$originalFilename}\nType: {$entityType}\nID: {$entityId}",
        ]);

        $data = $response->json();

        if (!$data['ok'] ?? false) {
            throw new \Exception("Telegram upload failed: " . json_encode($data));
        }

        $document = $data['result']['document'] ?? null;
        $fileId = $document['file_id'] ?? null;
        $telegramFileId = $document['file_unique_id'] ?? null;

        if (!$fileId) {
            throw new \Exception("No file_id returned from Telegram");
        }

        // Get file path for later retrieval
        $fileInfo = $this->getFileInfo($fileId);
        $fileTelegramPath = $fileInfo['file_path'] ?? null;

        // Store metadata
        $record = DB::table('telegram_files')->insert([
            'telegram_file_id' => $fileId,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'file_path' => $fileTelegramPath,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'stored_at' => 'group_storage',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'telegram_file_id' => $fileId,
            'original_filename' => $originalFilename,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
        ];
    }

    public function downloadFile(string $telegramFileId): string
    {
        $fileInfo = $this->getFileInfo($telegramFileId);
        $filePath = $fileInfo['file_path'] ?? null;

        if (!$filePath) {
            throw new \Exception("Cannot get file path for: $telegramFileId");
        }

        $fileUrl = "{$this->filesApiUrl()}/file/bot{$this->botToken}/{$filePath}";

        $response = Http::get($fileUrl);

        if ($response->failed()) {
            throw new \Exception("Failed to download file from Telegram");
        }

        return $response->body();
    }

    public function getFileInfo(string $fileId): array
    {
        $response = Http::post("{$this->apiUrl()}/getFile", [
            'file_id' => $fileId,
        ]);

        $data = $response->json();

        if (!$data['ok'] ?? false) {
            throw new \Exception("getFile failed: " . json_encode($data));
        }

        return $data['result'] ?? [];
    }

    public function syncFilesFromGroup(?int $limit = 100): array
    {
        // Get recent messages from group
        $response = Http::post("{$this->apiUrl()}/getChat", [
            'chat_id' => $this->groupStorageId,
        ]);

        $data = $response->json();

        if (!$data['ok'] ?? false) {
            Log::error('Telegram getChat failed', $data);
            return [];
        }

        // Use getUpdates to fetch messages (cheaper API call)
        $updates = $this->getRecentMessages($limit);

        $synced = [];

        foreach ($updates as $update) {
            $message = $update['message'] ?? null;

            if (!$message || $message['chat']['id'] != $this->groupStorageId) {
                continue;
            }

            // Handle different file types
            if ($document = $message['document'] ?? null) {
                $synced[] = $this->recordFile(
                    $document['file_id'],
                    $document['file_unique_id'] ?? null,
                    $document['file_name'] ?? 'unknown',
                    $document['mime_type'] ?? null,
                    $document['file_size'] ?? null,
                    $message['caption'] ?? null
                );
            } elseif ($photo = $message['photo'][0] ?? null) {
                $synced[] = $this->recordFile(
                    $photo['file_id'],
                    $photo['file_unique_id'] ?? null,
                    'photo_' . date('YmdHis') . '.jpg',
                    'image/jpeg',
                    $photo['file_size'] ?? null,
                    $message['caption'] ?? null
                );
            }
        }

        return $synced;
    }

    private function recordFile(
        string $fileId,
        ?string $fileUniqueId,
        string $filename,
        ?string $mimeType,
        ?int $fileSize,
        ?string $caption
    ): array {
        $existing = DB::table('telegram_files')
            ->where('telegram_file_id', $fileId)
            ->first();

        if ($existing) {
            return ['status' => 'skipped', 'file_id' => $fileId];
        }

        $fileInfo = $this->getFileInfo($fileId);
        $filePath = $fileInfo['file_path'] ?? null;

        // Parse caption for entity_type and entity_id
        $entityType = null;
        $entityId = null;

        if ($caption) {
            preg_match('/Type:\s*(\w+)/', $caption, $typeMatch);
            preg_match('/ID:\s*(\d+)/', $caption, $idMatch);

            $entityType = $typeMatch[1] ?? null;
            $entityId = $idMatch[1] ?? null;
        }

        DB::table('telegram_files')->insert([
            'telegram_file_id' => $fileId,
            'original_filename' => $filename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'file_path' => $filePath,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'stored_at' => 'group_storage',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'status' => 'synced',
            'file_id' => $fileId,
            'filename' => $filename,
        ];
    }

    private function getRecentMessages(int $limit = 100): array
    {
        // Fallback: use getUpdates for polling (no webhook needed)
        $offset = DB::table('telegram_files')
            ->select('*')
            ->orderByDesc('created_at')
            ->limit(1)
            ->pluck('created_at')
            ->first();

        // getUpdates is limited and won't give group messages reliably
        // Instead, we'll fetch via API endpoint that requires admin token
        // For now, return empty; production needs alternative polling method

        Log::info('Telegram sync: fallback polling (limited without admin access)');

        return [];
    }

    public function logToGroup(
        string $level,
        string $domain,
        string $action,
        string $detail,
        ?string $actor = null
    ): bool {
        $timestamp = now()->format('Y-m-d H:i:s');

        $text = "[{$timestamp}] [{$level}] [{$domain}]";

        if ($actor) {
            $text .= " [{$actor}]";
        }

        $text .= " [{$action}] {$detail}";

        $response = Http::post("{$this->apiUrl()}/sendMessage", [
            'chat_id' => $this->groupLogId,
            'text' => $text,
        ]);

        $data = $response->json();

        return $data['ok'] ?? false;
    }

    private function apiUrl(): string
    {
        return self::API_BASE . "/bot{$this->botToken}";
    }

    private function filesApiUrl(): string
    {
        return 'https://api.telegram.org';
    }
}
