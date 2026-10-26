<?php

namespace App\Services\Doku;

class DokuSignature
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $secretKey,
    ) {}

    /**
     * Bangun header Signature non-SNAP. $rawBody null = GET (tanpa Digest).
     */
    public function sign(string $requestId, string $timestamp, string $requestTarget, ?string $rawBody = null): string
    {
        $component = 'Client-Id:'.$this->clientId."\n".
            'Request-Id:'.$requestId."\n".
            'Request-Timestamp:'.$timestamp."\n".
            'Request-Target:'.$requestTarget;

        if ($rawBody !== null) {
            $component .= "\nDigest:".base64_encode(hash('sha256', $rawBody, true));
        }

        return 'HMACSHA256='.base64_encode(hash_hmac('sha256', $component, $this->secretKey, true));
    }

    public function verify(string $signature, string $requestId, string $timestamp, string $requestTarget, ?string $rawBody = null): bool
    {
        // Tolak kalau kredensial belum diisi: HMAC dengan secret kosong bisa dipalsukan attacker.
        if ($this->secretKey === '' || $this->clientId === '') {
            return false;
        }

        return hash_equals($this->sign($requestId, $timestamp, $requestTarget, $rawBody), $signature);
    }

    public function clientId(): string
    {
        return $this->clientId;
    }
}
