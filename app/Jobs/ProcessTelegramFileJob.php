<?php

namespace App\Jobs;

use App\Services\TelegramStorageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessTelegramFileJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $fileId,
        private string $fileType, // 'document' or 'photo'
        private string $filename,
        private ?string $mimeType,
        private ?int $fileSize,
        private ?string $caption,
    ) {
    }

    public function handle(TelegramStorageService $storage): void
    {
        try {
            $fileInfo = $storage->getFileInfo($this->fileId);
            $filePath = $fileInfo['file_path'] ?? null;

            $entityType = null;
            $entityId = null;

            if ($this->caption) {
                preg_match('/Type:\s*(\w+)/', $this->caption, $typeMatch);
                preg_match('/ID:\s*(\d+)/', $this->caption, $idMatch);
                $entityType = $typeMatch[1] ?? null;
                $entityId = $idMatch[1] ?? null;
            }

            DB::table('telegram_files')->insertOrIgnore([
                'telegram_file_id' => $this->fileId,
                'original_filename' => $this->filename,
                'mime_type' => $this->mimeType,
                'file_size' => $this->fileSize,
                'file_path' => $filePath,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'stored_at' => 'group_storage',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $storage->logToGroup(
                'INFO',
                'telegram_file_job',
                'file_processed',
                "Type: {$this->fileType} | File: {$this->filename} | Size: {$this->fileSize} bytes | ID: {$this->fileId}"
            );

            Log::info("Telegram file processed: {$this->filename}");
        } catch (\Exception $e) {
            Log::error("Failed to process Telegram file: {$this->filename}", [
                'error' => $e->getMessage(),
                'file_id' => $this->fileId,
            ]);

            $storage->logToGroup(
                'ERROR',
                'telegram_file_job',
                'file_error',
                "Failed: {$this->filename} - " . $e->getMessage()
            );

            throw $e;
        }
    }
}
