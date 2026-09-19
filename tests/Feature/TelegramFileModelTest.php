<?php

use App\Models\TelegramFile;

// TelegramFile model: status helpers & state transitions

it('starts as pending by default', function () {
    $file = TelegramFile::factory()->create();

    expect($file->isPending())->toBeTrue()
        ->and($file->isUploaded())->toBeFalse()
        ->and($file->isFailed())->toBeFalse();
});

it('detects uploaded status', function () {
    $file = TelegramFile::factory()->uploaded()->create();

    expect($file->isUploaded())->toBeTrue()
        ->and($file->isPending())->toBeFalse()
        ->and($file->isFailed())->toBeFalse();
});

it('detects failed status', function () {
    $file = TelegramFile::factory()->failed()->create();

    expect($file->isFailed())->toBeTrue()
        ->and($file->isPending())->toBeFalse()
        ->and($file->isUploaded())->toBeFalse();
});

it('can mark as uploaded', function () {
    $file = TelegramFile::factory()->create();

    $file->markUploaded();

    expect($file->fresh()->backup_status)->toBe('uploaded')
        ->and($file->fresh()->last_attempted_at)->not->toBeNull();
});

it('can mark as failed with error message and increment retry', function () {
    $file = TelegramFile::factory()->create(['retry_count' => 0]);

    $file->markFailed('Connection timeout');

    $fresh = $file->fresh();
    expect($fresh->backup_status)->toBe('failed')
        ->and($fresh->retry_count)->toBe(1)
        ->and($fresh->last_error)->toBe('Connection timeout')
        ->and($fresh->last_attempted_at)->not->toBeNull();
});

it('increments retry_count on successive failures', function () {
    $file = TelegramFile::factory()->create(['retry_count' => 2]);

    $file->markFailed('Rate limited');

    expect($file->fresh()->retry_count)->toBe(3);
});

it('can reset to pending and clear error', function () {
    $file = TelegramFile::factory()->failed()->create();

    $file->markPending();

    $fresh = $file->fresh();
    expect($fresh->backup_status)->toBe('pending')
        ->and($fresh->last_error)->toBeNull();
});
