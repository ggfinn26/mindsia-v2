<?php

use App\Models\ApiKey;
use App\Services\Letter\ConvertApiService;
use App\Services\TelegramLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('rotasi ke key cadangan + alert telegram saat key kena 403 kuota habis', function () {
    ApiKey::create(['type' => 'convertapi', 'label' => 'Key 1', 'key_value' => 'dead-key', 'is_active' => true]);
    ApiKey::create(['type' => 'convertapi', 'label' => 'Key 2', 'key_value' => 'live-key', 'is_active' => true]);

    Http::fake([
        'v2.convertapi.com/*' => Http::sequence()
            ->push([], 403)
            ->push(['Files' => [['Url' => 'https://cdn.test/out.pdf']]], 200),
        'cdn.test/out.pdf' => Http::response('PDFBYTES', 200),
    ]);

    $telegram = $this->mock(TelegramLogService::class);
    $telegram->shouldReceive('log')->atLeast()->once();

    $path = tempnam(sys_get_temp_dir(), 'docx');
    file_put_contents($path, 'dummy');

    $pdf = app(ConvertApiService::class)->docxToPdf($path);

    unlink($path);

    expect($pdf)->toBe('PDFBYTES');
    expect(ApiKey::where('label', 'Key 1')->first()->is_active)->toBeFalse();
    expect(ApiKey::where('label', 'Key 2')->first()->is_active)->toBeTrue();
});

it('alert telegram saat semua key habis', function () {
    ApiKey::create(['type' => 'convertapi', 'label' => 'Key 1', 'key_value' => 'dead-key', 'is_active' => true]);
    // Env fallback juga mati, agar loop habis sampai alert "semua key habis" (bukan throw).
    config()->set('services.convertapi.key', 'env-dead-key');

    Http::fake(['v2.convertapi.com/*' => Http::response([], 403)]);

    $telegram = $this->mock(TelegramLogService::class);
    $telegram->shouldReceive('log')->atLeast()->once();

    $path = tempnam(sys_get_temp_dir(), 'docx');
    file_put_contents($path, 'dummy');

    $pdf = app(ConvertApiService::class)->docxToPdf($path);

    unlink($path);

    expect($pdf)->toBeNull();
});
