<?php

use App\Jobs\PushTelegramBackupJob;
use App\Models\TelegramFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

// PushTelegramBackupJob — async backup to Telegram with retry & rate limiting

beforeEach(function () {
    Queue::fake();

    // Set required config for job execution
    config(['services.telegram.bot_token' => 'test-bot-token']);
    config(['services.telegram.chat_id' => 'test-chat-id']);
});

it('skips if TelegramFile record not found', function () {
    $job = new PushTelegramBackupJob(99999, 'documents/test.pdf', 'test.pdf');

    // Mock Redis throttle to execute immediately
    Redis::shouldReceive('throttle')->andReturnSelf();
    Redis::shouldReceive('allow')->andReturnSelf();
    Redis::shouldReceive('every')->andReturnSelf();
    Redis::shouldReceive('then')->andReturnUsing(fn ($callback) => $callback());

    $job->handle(app(\App\Services\TelegramStorageService::class));

    Http::assertNothingSent();
});

it('skips if record is already uploaded', function () {
    $file = TelegramFile::factory()->uploaded()->create();

    $job = new PushTelegramBackupJob($file->id, $file->storage_path, $file->original_filename);

    $job->handle(app(\App\Services\TelegramStorageService::class));

    Http::assertNothingSent();
    expect($file->fresh()->backup_status)->toBe('uploaded');
});

it('marks as failed when local file does not exist', function () {
    $file = TelegramFile::factory()->create([
        'storage_path' => 'nonexistent/file.pdf',
        'backup_status' => 'pending',
    ]);

    $job = new PushTelegramBackupJob($file->id, 'nonexistent/file.pdf', 'file.pdf');

    $job->handle(app(\App\Services\TelegramStorageService::class));

    $fresh = $file->fresh();
    expect($fresh->backup_status)->toBe('failed')
        ->and($fresh->last_error)->toStartWith('Local file not found');
});

it('uploads photo to Telegram and marks as uploaded', function () {
    // Create real file via Storage (matches default disk path resolution)
    $path = 'test-backup-photo.webp';
    Storage::put($path, str_repeat("\x00", 100)); // minimal binary

    $file = TelegramFile::factory()->create([
        'storage_path' => $path,
        'mime_type' => 'image/webp',
        'backup_status' => 'pending',
    ]);

    $this->setHttpFakes([
        'https://api.telegram.org/*' => Http::response([
            'ok' => true,
            'result' => [
                'photo' => [
                    ['file_id' => 'small_id'],
                    ['file_id' => 'medium_id'],
                    ['file_id' => 'real_telegram_file_id'],
                ],
            ],
        ], 200),
    ]);

    Redis::shouldReceive('throttle')->andReturnSelf();
    Redis::shouldReceive('allow')->andReturnSelf();
    Redis::shouldReceive('every')->andReturnSelf();
    Redis::shouldReceive('then')->andReturnUsing(fn ($callback) => $callback());

    $job = new PushTelegramBackupJob($file->id, $path, 'photo.webp', caption: 'Test photo');

    $job->handle($service = app(\App\Services\TelegramStorageService::class));

    $fresh = $file->fresh();
    // Debug: show actual error if not uploaded
    if ($fresh->backup_status !== 'uploaded') {
        dump(['status' => $fresh->backup_status, 'error' => $fresh->last_error, 'file_exists' => file_exists(Storage::path($path)), 'abs_path' => Storage::path($path)]);
    }
    expect($fresh->backup_status)->toBe('uploaded')
        ->and($fresh->telegram_file_id)->toBe('real_telegram_file_id')
        ->and($fresh->last_attempted_at)->not->toBeNull();

    Http::assertSentCount(1);

    // Cleanup
    Storage::delete($path);
});

it('uploads document to Telegram and marks as uploaded', function () {
    $path = 'test-backup-doc.pdf';
    Storage::put($path, '%PDF-1.4 fake content');

    $file = TelegramFile::factory()->create([
        'storage_path' => $path,
        'mime_type' => 'application/pdf',
        'backup_status' => 'pending',
    ]);

    $this->setHttpFakes([
        'https://api.telegram.org/*' => Http::response([
            'ok' => true,
            'result' => [
                'document' => ['file_id' => 'doc_telegram_file_id'],
            ],
        ], 200),
    ]);

    Redis::shouldReceive('throttle')->andReturnSelf();
    Redis::shouldReceive('allow')->andReturnSelf();
    Redis::shouldReceive('every')->andReturnSelf();
    Redis::shouldReceive('then')->andReturnUsing(fn ($callback) => $callback());

    $job = new PushTelegramBackupJob($file->id, $path, 'doc.pdf', caption: 'Test doc');

    $job->handle(app(\App\Services\TelegramStorageService::class));

    $fresh = $file->fresh();
    expect($fresh->backup_status)->toBe('uploaded')
        ->and($fresh->telegram_file_id)->toBe('doc_telegram_file_id');

    Storage::delete($path);
});

it('marks as failed when Telegram API returns error', function () {
    $path = 'test-backup-fail.pdf';
    Storage::put($path, 'fake content');

    $file = TelegramFile::factory()->create([
        'storage_path' => $path,
        'mime_type' => 'application/pdf',
        'backup_status' => 'pending',
    ]);

    Http::fake([
        'api.telegram.org/*' => Http::response([
            'ok' => false,
            'description' => 'Bad Request: chat not found',
        ], 400),
    ]);

    Redis::shouldReceive('throttle')->andReturnSelf();
    Redis::shouldReceive('allow')->andReturnSelf();
    Redis::shouldReceive('every')->andReturnSelf();
    Redis::shouldReceive('then')->andReturnUsing(fn ($callback) => $callback());

    $job = new PushTelegramBackupJob($file->id, $path, 'fail.pdf');

    $job->handle(app(\App\Services\TelegramStorageService::class));

    $fresh = $file->fresh();
    expect($fresh->backup_status)->toBe('failed')
        ->and($fresh->last_error)->toContain('Telegram API error');

    Storage::delete($path);
});

it('has correct retry configuration', function () {
    $job = new PushTelegramBackupJob(1, 'test.pdf', 'test.pdf');

    expect($job->tries)->toBe(3)
        ->and($job->backoff)->toBe([60, 300, 900]);
});

it('uses telegram-backup queue', function () {
    $job = new PushTelegramBackupJob(1, 'test.pdf', 'test.pdf');

    expect($job->queue)->toBe('telegram-backup');
});
