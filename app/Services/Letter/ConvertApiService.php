<?php

namespace App\Services\Letter;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConvertApiService
{
    private const ENDPOINT = 'https://v2.convertapi.com/convert/docx/to/pdf';

    /**
     * Convert a local DOCX file to PDF binary.
     * Throws \RuntimeException if no API key is configured.
     */
    public function docxToPdf(string $localDocxPath): ?string
    {
        if (! file_exists($localDocxPath)) {
            Log::error("ConvertApi: file not found at {$localDocxPath}");

            return null;
        }

        $maxRetries = 5;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $keyRecord = $this->resolveKeyRecord();
            $keyValue = $keyRecord ? $keyRecord->key_value : config('services.convertapi.key', env('CONVERSION_API', ''));

            if (! $keyValue) {
                throw new \RuntimeException('Tidak ada ConvertAPI key aktif. Tambahkan key di System → API Keys atau set CONVERSION_API di .env.');
            }

            // Auth via Bearer header — never put API key in URL (would appear in access logs)
            $response = Http::withToken($keyValue)
                ->attach('File', fopen($localDocxPath, 'r'), basename($localDocxPath))
                ->post(self::ENDPOINT.'?StoreFile=true');

            if ($response->status() === 429) {
                Log::warning('ConvertApi 429 Rate Limit hit.', ['key_id' => $keyRecord?->id]);

                // Nonaktifkan key yang sudah limit
                if ($keyRecord) {
                    $keyRecord->update(['is_active' => false]);
                }

                // Lanjut ke loop berikutnya untuk mencoba key lain
                continue;
            }

            if ($response->failed()) {
                Log::error('ConvertApi HTTP error', ['status' => $response->status()]);

                return null;
            }

            $data = $response->json();
            $pdfUrl = $data['Files'][0]['Url'] ?? null;

            if (! $pdfUrl) {
                Log::error('ConvertApi: no PDF URL in response');

                return null;
            }

            $pdfResponse = Http::get($pdfUrl);

            if ($pdfResponse->failed()) {
                Log::error('ConvertApi: failed to download converted PDF');

                return null;
            }

            return $pdfResponse->body();
        }

        Log::error('ConvertApi: All keys exhausted or reached max retries due to 429 Rate Limit');

        return null;
    }

    private function resolveKeyRecord(): ?ApiKey
    {
        // Pick active key least recently used, then mark it used
        $record = ApiKey::active()
            ->where('type', 'convertapi')
            ->orderByRaw('last_used_at IS NOT NULL, last_used_at ASC')
            ->first();

        if ($record) {
            $record->update(['last_used_at' => now()]);
        }

        return $record;
    }
}
