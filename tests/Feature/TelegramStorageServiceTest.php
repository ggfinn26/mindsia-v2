<?php

use App\Jobs\PushTelegramBackupJob;
use App\Models\TelegramFile;
use App\Services\TelegramStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

// TelegramStorageService — local-first with async Telegram backup

beforeEach(function () {
    Queue::fake();
    Http::fake();

    // Set required config for Telegram API fallback
    config(['services.telegram.bot_token' => 'test-bot-token']);
    config(['services.telegram.chat_id' => 'test-chat-id']);
});

afterEach(function () {
    // Cleanup test files from storage disk
    $patterns = ['test-tg-*', 'landing/test-tg-*', 'documents/test-tg-*', 'payroll/test-tg-*', 'letters/test-tg-*', 'kpi/test-tg-*'];
    foreach ($patterns as $pattern) {
        $files = Storage::files(dirname($pattern) === '.' ? '' : dirname($pattern));
        foreach ($files as $file) {
            if (str_contains(basename($file), 'test-tg-')) {
                Storage::delete($file);
            }
        }
    }
});

// ─── uploadPhoto ────────────────────────────────────────────────

it('converts image to webp and stores locally', function () {
    $file = UploadedFile::fake()->image('test-tg-photo.jpg', 100, 100);

    $result = app(TelegramStorageService::class)->uploadPhoto($file, 'Test caption');

    expect($result)->toEndWith('.webp')
        ->and(Storage::exists($result))->toBeTrue();
});

it('stores gif as-is without conversion', function () {
    $file = UploadedFile::fake()->create('test-tg-animation.gif', 100, 'image/gif');

    $result = app(TelegramStorageService::class)->uploadPhoto($file, 'Gif test');

    expect($result)->toEndWith('.gif')
        ->and(Storage::exists($result))->toBeTrue();
});

it('stores webp as-is without conversion', function () {
    // Create a minimal 1x1 webp file
    $tmpPath = tempnam(sys_get_temp_dir(), 'webp').'.webp';
    $img = imagecreatetruecolor(1, 1);
    imagewebp($img, $tmpPath);
    imagedestroy($img);

    $file = new UploadedFile($tmpPath, 'test-tg-photo.webp', 'image/webp', null, true);

    $result = app(TelegramStorageService::class)->uploadPhoto($file, 'WebP test');

    expect($result)->toEndWith('.webp')
        ->and(Storage::exists($result))->toBeTrue();

    @unlink($tmpPath);
});

it('dispatches backup job after photo upload', function () {
    $file = UploadedFile::fake()->image('test-tg-photo.jpg', 50, 50);

    app(TelegramStorageService::class)->uploadPhoto($file, 'Backup test');

    Queue::assertPushed(PushTelegramBackupJob::class, 1);
});

it('creates TelegramFile record with pending status after photo upload', function () {
    $file = UploadedFile::fake()->image('test-tg-photo.jpg', 50, 50);

    $storagePath = app(TelegramStorageService::class)->uploadPhoto($file, 'Record test');

    $this->assertDatabaseHas('telegram_files', [
        'storage_path' => $storagePath,
        'backup_status' => 'pending',
    ]);
});

// ─── uploadFile ─────────────────────────────────────────────────

it('stores file to local disk and returns file_id', function () {
    // Write a source file to storage
    $sourceContent = 'Hello World';
    $sourceKey = 'test-tg-upload-source.txt';
    Storage::put($sourceKey, $sourceContent);
    $sourcePath = Storage::path($sourceKey);

    $result = app(TelegramStorageService::class)->uploadFile($sourcePath, 'test-tg-upload.txt', 'document');

    expect($result)->toHaveKey('file_id')
        ->and($result['file_id'])->toBeString()
        ->and(Storage::exists($result['file_id']))->toBeTrue();

    Storage::delete($sourceKey);
});

it('routes payroll files to payroll directory', function () {
    Storage::put('test-tg-slip-source.txt', 'payroll content');
    $sourcePath = Storage::path('test-tg-slip-source.txt');

    $result = app(TelegramStorageService::class)->uploadFile($sourcePath, 'slip.pdf', 'payroll_slip');

    expect($result['file_id'])->toStartWith('payroll/');

    Storage::delete('test-tg-slip-source.txt');
});

it('routes warning letters to letters directory', function () {
    Storage::put('test-tg-warning-source.txt', 'letter content');
    $sourcePath = Storage::path('test-tg-warning-source.txt');

    $result = app(TelegramStorageService::class)->uploadFile($sourcePath, 'warning.pdf', 'warning_letter');

    expect($result['file_id'])->toStartWith('letters/');

    Storage::delete('test-tg-warning-source.txt');
});

it('dispatches backup job after file upload', function () {
    Storage::put('test-tg-dispatch-source.txt', 'content');
    $sourcePath = Storage::path('test-tg-dispatch-source.txt');

    app(TelegramStorageService::class)->uploadFile($sourcePath, 'source.txt');

    Queue::assertPushed(PushTelegramBackupJob::class, 1);

    Storage::delete('test-tg-dispatch-source.txt');
});

// ─── downloadFile ───────────────────────────────────────────────

it('reads from local disk first', function () {
    $path = 'test-tg-local.pdf';
    Storage::put($path, '%PDF-1.4 local content');

    $content = app(TelegramStorageService::class)->downloadFile($path);

    expect($content)->toBe('%PDF-1.4 local content');

    Storage::delete($path);
});

it('falls back to Telegram when local file is missing', function () {
    $file = TelegramFile::factory()->uploaded()->create([
        'storage_path' => 'test-tg-missing-local.pdf',
        'telegram_file_id' => 'AgAC_real_telegram_id',
    ]);

    $this->setHttpFakes([
        'https://api.telegram.org/*' => fn ($request) => str_contains($request->url(), '/getFile')
            ? Http::response(['ok' => true, 'result' => ['file_path' => 'documents/file.pdf']], 200)
            : Http::response('telegram file content', 200),
    ]);

    $content = app(TelegramStorageService::class)->downloadFile('test-tg-missing-local.pdf');

    expect($content)->toBe('telegram file content');

    // Should restore to local disk
    expect(Storage::exists('test-tg-missing-local.pdf'))->toBeTrue();

    Storage::delete('test-tg-missing-local.pdf');
});

// ─── getFileUrl ─────────────────────────────────────────────────

it('returns URL when file exists locally', function () {
    $path = 'test-tg-local-photo.webp';
    Storage::put($path, 'fake content');

    $url = app(TelegramStorageService::class)->getFileUrl($path);

    expect($url)->not->toBeNull();

    Storage::delete($path);
});

it('returns null for empty file ID', function () {
    $url = app(TelegramStorageService::class)->getFileUrl('');

    expect($url)->toBeNull();
});

it('falls back to Telegram URL for legacy telegram_file_id', function () {
    TelegramFile::factory()->uploaded()->create([
        'telegram_file_id' => 'legacy_id_123',
        'storage_path' => null,
    ]);

    Http::fake([
        'api.telegram.org/bot*/getFile*' => Http::response([
            'ok' => true,
            'result' => ['file_path' => 'photos/file.jpg'],
        ], 200),
    ]);

    $url = app(TelegramStorageService::class)->getFileUrl('legacy_id_123');

    expect($url)->toContain('api.telegram.org');
});
