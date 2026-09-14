<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GalleryController extends Controller
{
    public function index()
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

        $files = DB::table('telegram_files')->orderBy('created_at', 'desc')->get();
        Log::info('Gallery: Found '.count($files).' files in DB');

        $items = [];
        foreach ($files as $file) {
            try {
                $response = Http::post("https://api.telegram.org/bot{$botToken}/getFile", [
                    'file_id' => $file->telegram_file_id,
                ]);

                $data = $response->json();
                if ($data['ok'] ?? false) {
                    $filePath = $data['result']['file_path'];
                    $downloadUrl = "https://api.telegram.org/file/bot{$botToken}/{$filePath}";
                    $proxyUrl = route('file.serve', $file->telegram_file_id);

                    $type = $this->getFileType($file->mime_type, $file->original_filename);

                    $items[] = [
                        'filename' => $file->original_filename,
                        'url' => $downloadUrl,
                        'proxyUrl' => $proxyUrl,
                        'type' => $type,
                        'mime_type' => $file->mime_type,
                        'file_size' => $file->file_size,
                        'file_size_formatted' => $this->formatFileSize($file->file_size),
                        'created_at' => $file->created_at,
                    ];

                    Log::info('Gallery: Added '.$file->original_filename);
                } else {
                    Log::warning('Gallery: Failed to get file info for '.$file->original_filename);
                }
            } catch (\Exception $e) {
                Log::error('Gallery: Exception for '.$file->original_filename.': '.$e->getMessage());
            }
        }

        Log::info('Gallery: Rendering view with '.count($items).' items');

        return view('gallery', ['items' => $items]);
    }

    private function getFileType(?string $mimeType, string $filename): string
    {
        if (str_contains($mimeType ?? '', 'image')) {
            return 'image';
        }
        if (str_contains($mimeType ?? '', 'pdf')) {
            return 'pdf';
        }
        if (str_contains($mimeType ?? '', 'word') || str_ends_with($filename, '.docx')) {
            return 'word';
        }
        if (str_contains($mimeType ?? '', 'text')) {
            return 'text';
        }

        return 'file';
    }

    private function formatFileSize(?int $bytes): string
    {
        if (! $bytes || $bytes === 0) {
            return '0 Bytes';
        }

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2).' '.$sizes[$i];
    }
}
