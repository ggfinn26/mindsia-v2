<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FileProxyController extends Controller
{
    public function serve(string $fileId)
    {
        // Serve from local disk — allowlist only, no path traversal
        $publicPrefixes = ['landing/', 'photos/', 'payroll/', 'letters/', 'kpi/', 'documents/'];
        $isPublicPath = str_contains($fileId, '/')
            && ! str_contains($fileId, '..')
            && collect($publicPrefixes)->contains(fn ($p) => str_starts_with($fileId, $p));

        if ($isPublicPath && Storage::exists($fileId)) {
            $content = Storage::get($fileId);
            $mimeType = $this->detectMimeType($fileId);

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', strlen($content))
                ->header('Cache-Control', 'public, max-age=3600');
        }

        $botToken = env('TELEGRAM_BOT_TOKEN');

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/getFile", [
                'file_id' => $fileId,
            ]);

            $data = $response->json();

            if (! ($data['ok'] ?? false)) {
                abort(404, 'File not found');
            }

            $filePath = $data['result']['file_path'];
            $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$filePath}";

            $fileResponse = Http::get($fileUrl);

            if ($fileResponse->failed()) {
                abort(404, 'Could not fetch file');
            }

            $content = $fileResponse->body();
            $mimeType = $fileResponse->header('Content-Type')
                ?: $this->detectMimeType($filePath);

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', strlen($content))
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            abort(500, 'Error fetching file: '.$e->getMessage());
        }
    }

    private function detectMimeType(string $url): string
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
        if (str_contains($url, '.webp')) {
            return 'image/webp';
        }

        return 'application/octet-stream';
    }
}
