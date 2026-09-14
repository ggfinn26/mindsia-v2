<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramStorageService
{
    protected $botToken;

    protected $chatId;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('GROUP_STORAGE'); // ID dari channel/grup arsip
    }

    /**
     * Mengunggah foto ke Telegram dan mengembalikan file_id
     */
    public function uploadPhoto(UploadedFile $file, $caption = '')
    {
        if (! $this->botToken || ! $this->chatId) {
            throw new Exception('TELEGRAM_BOT_TOKEN atau GROUP_STORAGE belum diatur di .env');
        }

        $response = Http::attach(
            'photo', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
        )->post("https://api.telegram.org/bot{$this->botToken}/sendPhoto", [
            'chat_id' => $this->chatId,
            'caption' => $caption,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            // Telegram mengirimkan beberapa resolusi, ambil yang terakhir (terbesar)
            $photos = $data['result']['photo'];
            $fileId = end($photos)['file_id'];

            return $fileId;
        }

        Log::error('Telegram upload failed', ['response' => $response->json()]);
        throw new Exception('Gagal mengunggah foto ke Telegram: '.$response->body());
    }

    /**
     * Mendapatkan URL langsung ke file di server Telegram
     */
    public function getFileUrl($fileId)
    {
        if (! $fileId) {
            return null;
        }

        $response = Http::get("https://api.telegram.org/bot{$this->botToken}/getFile", [
            'file_id' => $fileId,
        ]);

        if ($response->successful()) {
            $filePath = $response->json()['result']['file_path'];

            return "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";
        }

        return null;
    }
}
