<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileProxyController extends Controller
{
    public function serve(string $fileId)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/getFile", [
                'file_id' => $fileId,
            ]);

            $data = $response->json();

            if (!($data['ok'] ?? false)) {
                abort(404, 'File not found');
            }

            $filePath = $data['result']['file_path'];
            $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$filePath}";

            $fileResponse = Http::get($fileUrl);

            if ($fileResponse->failed()) {
                abort(404, 'Could not fetch file');
            }

            $content = $fileResponse->body();
            $mimeType = $this->detectMimeType($fileUrl, strlen($content));

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', strlen($content))
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            abort(500, 'Error fetching file: ' . $e->getMessage());
        }
    }

    private function detectMimeType(string $url, int $size): string
    {
        if (str_contains($url, '.pdf')) {
            return 'application/pdf';
        }
        if (str_contains($url, '.docx')) {
            return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        }
        if (str_contains($url, '.jpg') || str_contains($url, '.jpeg')) {
            return 'image/jpeg';
        }
        if (str_contains($url, '.png')) {
            return 'image/png';
        }

        return 'application/octet-stream';
    }
}
