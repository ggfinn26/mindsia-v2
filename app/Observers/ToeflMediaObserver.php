<?php

namespace App\Observers;

use App\Models\ToeflMedia;
use App\Services\TelegramLogService;

class ToeflMediaObserver
{
    public function __construct(
        private readonly TelegramLogService $telegramLogService,
    ) {}

    public function created(ToeflMedia $media): void
    {
        $this->telegramLogService->log('INFO', 'toefl', 'upload_toefl_media', "Media TOEFL diunggah: {$media->original_name} (Tipe: {$media->media_type})");
    }
}
