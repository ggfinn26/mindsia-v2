<?php

namespace App\Services;

use App\Jobs\PushTelegramBackupJob;
use App\Models\TelegramFile;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class TelegramStorageService
{
    protected $botToken;

    protected $chatId;

    protected ?ImageManager $imageManager = null;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    protected function imageManager(): ImageManager
    {
        if (! $this->imageManager) {
            $this->imageManager = new ImageManager(new GdDriver);
        }

        return $this->imageManager;
    }

    /**
     * Upload photo — convert to WebP, save local, dispatch async backup to Telegram.
     * Returns the local storage path (primary).
     */
    public function uploadPhoto(UploadedFile $file, string $caption = '', string $directory = 'photos'): string
    {
        $storagePath = $this->convertAndStore($file, $directory);
        $originalFilename = $file->getClientOriginalName();

        $this->dispatchBackup($storagePath, $originalFilename, 'photo', $caption);

        return $storagePath;
    }

    /**
     * Upload file (document) — save local, dispatch async backup to Telegram.
     * Returns ['file_id' => $storagePath] — interface preserved for callers.
     */
    public function uploadFile(string $localPath, string $filename, string $type = 'document', ?int $referenceId = null): array
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $directory = $this->directoryForType($type);
        $storagePath = $directory.'/'.$filename;

        Storage::put($storagePath, file_get_contents($localPath));

        $caption = "{$type}:{$referenceId}";
        $this->dispatchBackup($storagePath, $filename, $type, $caption);

        return ['file_id' => $storagePath];
    }

    /**
     * Download file — read from local disk first, fallback to Telegram.
     */
    public function downloadFile(string $fileId): string
    {
        // Try local disk first
        if (Storage::exists($fileId)) {
            return Storage::get($fileId);
        }

        // Fallback: fetch from Telegram (disaster recovery)
        $telegramFile = TelegramFile::where('storage_path', $fileId)
            ->orWhere('telegram_file_id', $fileId)
            ->first();

        if ($telegramFile?->telegram_file_id) {
            $content = $this->downloadFromTelegram($telegramFile->telegram_file_id);

            // Restore to local
            if ($telegramFile->storage_path) {
                Storage::put($telegramFile->storage_path, $content);
            }

            return $content;
        }

        // Last resort: try the fileId as a raw telegram_file_id
        return $this->downloadFromTelegram($fileId);
    }

    /**
     * Get file URL — always serve from local disk via Storage::url().
     */
    public function getFileUrl(string $fileId): ?string
    {
        if (! $fileId) {
            return null;
        }

        // If it looks like a storage path, serve locally
        if (Storage::exists($fileId)) {
            return Storage::url($fileId);
        }

        // Legacy fallback: it might be a raw telegram_file_id
        $telegramFile = TelegramFile::where('telegram_file_id', $fileId)->first();
        if ($telegramFile?->storage_path && Storage::exists($telegramFile->storage_path)) {
            return Storage::url($telegramFile->storage_path);
        }

        // Final fallback: serve from Telegram directly (legacy behavior)
        return $this->getTelegramFileUrl($fileId);
    }

    /**
     * Get info about a Telegram file (used by ProcessTelegramFileJob).
     */
    public function getFileInfo(string $fileId): ?array
    {
        $response = Http::get("https://api.telegram.org/bot{$this->botToken}/getFile", [
            'file_id' => $fileId,
        ]);

        if ($response->successful()) {
            return $response->json('result');
        }

        return null;
    }

    /**
     * Log a message to the Telegram log group (used by TelegramLogService).
     */
    public function logToGroup(string $level, string $category, string $action, string $message): void
    {
        $logChatId = config('services.telegram.log_chat_id');
        $botToken = $this->botToken;

        if (! $botToken || ! $logChatId) {
            return;
        }

        $text = "[{$level}] [{$category}] {$action}\n{$message}";

        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $logChatId,
            'text' => $text,
        ]);
    }

    // ─── Private helpers ──────────────────────────────────────────────

    private function convertAndStore(UploadedFile $file, string $directory = 'photos'): string
    {
        $mimeType = $file->getMimeType();
        $isImage = str_starts_with($mimeType, 'image/');

        if ($isImage && $mimeType !== 'image/webp' && $mimeType !== 'image/gif') {
            $image = $this->imageManager()->decodePath($file->getRealPath());
            $webp = $image->encode(new WebpEncoder(quality: 85));

            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.webp';
            $path = $directory.'/'.$filename;

            Storage::put($path, (string) $webp);

            return $path;
        }

        $filename = $file->getClientOriginalName();
        $path = $directory.'/'.$filename;

        Storage::put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    private function directoryForType(string $type): string
    {
        return match ($type) {
            'landing_photo' => 'landing',
            'payroll_slip' => 'payroll',
            'warning_letter' => 'letters',
            'kpi_document' => 'kpi',
            default => 'documents',
        };
    }

    private function dispatchBackup(string $storagePath, string $originalFilename, string $type, ?string $caption): void
    {
        $absolutePath = Storage::path($storagePath);

        $telegramFile = TelegramFile::create([
            'telegram_file_id' => 'pending_'.uniqid(),
            'original_filename' => $originalFilename,
            'mime_type' => mime_content_type($absolutePath) ?: null,
            'file_size' => filesize($absolutePath) ?: null,
            'storage_path' => $storagePath,
            'entity_type' => $type,
            'stored_at' => 'group_storage',
            'backup_status' => 'pending',
        ]);

        PushTelegramBackupJob::dispatch($telegramFile->id, $storagePath, $originalFilename, $caption);
    }

    private function downloadFromTelegram(string $telegramFileId): string
    {
        if (! $this->botToken) {
            throw new Exception('TELEGRAM_BOT_TOKEN belum diatur di .env');
        }

        $pathResponse = Http::get("https://api.telegram.org/bot{$this->botToken}/getFile", [
            'file_id' => $telegramFileId,
        ]);

        if ($pathResponse->failed()) {
            throw new Exception('Gagal mendapatkan path file dari Telegram');
        }

        $filePath = $pathResponse->json('result.file_path');
        $downloadUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";

        $content = Http::get($downloadUrl);

        if ($content->failed()) {
            throw new Exception('Gagal mengunduh file dari Telegram');
        }

        return $content->body();
    }

    private function getTelegramFileUrl(string $telegramFileId): ?string
    {
        $response = Http::get("https://api.telegram.org/bot{$this->botToken}/getFile", [
            'file_id' => $telegramFileId,
        ]);

        if ($response->successful()) {
            $filePath = $response->json('result.file_path');

            return "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";
        }

        return null;
    }
}
