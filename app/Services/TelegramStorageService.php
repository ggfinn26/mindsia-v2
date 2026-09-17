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
     * Upload file lokal (path) ke Telegram sebagai dokumen.
     * Returns ['file_id' => string].
     */
    public function uploadFile(string $localPath, string $filename, string $type = 'document', ?int $referenceId = null): array
    {
        if (! $this->botToken || ! $this->chatId) {
            throw new Exception('TELEGRAM_BOT_TOKEN atau GROUP_STORAGE belum diatur di .env');
        }

        if (! file_exists($localPath)) {
            throw new Exception("File tidak ditemukan: {$localPath}");
        }

        $response = Http::attach(
            'document', fopen($localPath, 'r'), $filename
        )->post("https://api.telegram.org/bot{$this->botToken}/sendDocument", [
            'chat_id' => $this->chatId,
            'caption' => "{$type}:{$referenceId}",
        ]);

        if ($response->successful()) {
            $fileId = $response->json()['result']['document']['file_id'];

            return ['file_id' => $fileId];
        }

        Log::error('Telegram uploadFile failed', ['response' => $response->json()]);
        throw new Exception('Gagal mengunggah file ke Telegram: '.$response->body());
    }

    /**
     * Download file dari Telegram, return raw binary content.
     */
    public function downloadFile(string $fileId): string
    {
        if (! $this->botToken) {
            throw new Exception('TELEGRAM_BOT_TOKEN belum diatur di .env');
        }

        $pathResponse = Http::get("https://api.telegram.org/bot{$this->botToken}/getFile", [
            'file_id' => $fileId,
        ]);

        if ($pathResponse->failed()) {
            throw new Exception('Gagal mendapatkan path file dari Telegram');
        }

        $filePath = $pathResponse->json()['result']['file_path'];
        $downloadUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";

        $content = Http::get($downloadUrl);

        if ($content->failed()) {
            throw new Exception('Gagal mengunduh file dari Telegram');
        }

        return $content->body();
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
