<?php

use App\Jobs\PushTelegramBackupJob;
use App\Models\TelegramFile;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Permission;

// TelegramBackupController — superadmin backup monitor

beforeEach(function () {
    Queue::fake();

    // Ensure permissions exist in test DB
    foreach (['view', 'retry', 'delete'] as $action) {
        Permission::firstOrCreate(['name' => "system.telegram_backup.{$action}", 'guard_name' => 'web']);
    }

    $this->superAdmin = User::factory()->create();
    $this->superAdmin->givePermissionTo([
        'system.telegram_backup.view',
        'system.telegram_backup.retry',
        'system.telegram_backup.delete',
    ]);
});

// ─── index ──────────────────────────────────────────────────────

it('can view backup list with permission', function () {
    TelegramFile::factory()->count(3)->create();

    $this->actingAs($this->superAdmin)
        ->get('/system/telegram-backup')
        ->assertOk()
        ->assertViewIs('system-access.telegram-backup.index')
        ->assertViewHas('files')
        ->assertViewHas('failedCount')
        ->assertViewHas('pendingCount');
});

it('denies access without view permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/system/telegram-backup')
        ->assertForbidden();
});

it('shows failed and pending counts', function () {
    TelegramFile::factory()->count(2)->failed()->create();
    TelegramFile::factory()->count(3)->create(); // pending

    $this->actingAs($this->superAdmin)
        ->get('/system/telegram-backup')
        ->assertOk()
        ->assertViewHas('failedCount', 2)
        ->assertViewHas('pendingCount', 3);
});

// ─── retry ──────────────────────────────────────────────────────

it('can retry a single failed backup', function () {
    $file = TelegramFile::factory()->failed()->create();

    $this->actingAs($this->superAdmin)
        ->post("/system/telegram-backup/{$file->id}/retry")
        ->assertRedirect(route('system.telegram-backup.index'));

    expect($file->fresh()->backup_status)->toBe('pending');
    Queue::assertPushed(PushTelegramBackupJob::class, 1);
});

it('skips retry for already uploaded file', function () {
    $file = TelegramFile::factory()->uploaded()->create();

    $this->actingAs($this->superAdmin)
        ->post("/system/telegram-backup/{$file->id}/retry")
        ->assertRedirect(route('system.telegram-backup.index'))
        ->assertSessionHas('info');

    Queue::assertNotPushed(PushTelegramBackupJob::class);
});

it('denies retry without retry permission', function () {
    $file = TelegramFile::factory()->failed()->create();
    $viewer = User::factory()->create();
    $viewer->givePermissionTo('system.telegram_backup.view');

    $this->actingAs($viewer)
        ->post("/system/telegram-backup/{$file->id}/retry")
        ->assertForbidden();
});

// ─── retryAll ───────────────────────────────────────────────────

it('can bulk retry all failed backups', function () {
    $files = TelegramFile::factory()->count(3)->failed()->create();

    $this->actingAs($this->superAdmin)
        ->post('/system/telegram-backup/retry-all')
        ->assertRedirect(route('system.telegram-backup.index'))
        ->assertSessionHas('success');

    foreach ($files as $file) {
        expect($file->fresh()->backup_status)->toBe('pending');
    }

    Queue::assertPushed(PushTelegramBackupJob::class, 3);
});

it('shows info when no failed backups exist', function () {
    TelegramFile::factory()->count(2)->uploaded()->create();

    $this->actingAs($this->superAdmin)
        ->post('/system/telegram-backup/retry-all')
        ->assertRedirect(route('system.telegram-backup.index'))
        ->assertSessionHas('info');

    Queue::assertNotPushed(PushTelegramBackupJob::class);
});

// ─── destroy ────────────────────────────────────────────────────

it('can delete a backup record', function () {
    $file = TelegramFile::factory()->create();

    $this->actingAs($this->superAdmin)
        ->delete("/system/telegram-backup/{$file->id}")
        ->assertRedirect(route('system.telegram-backup.index'));

    expect(TelegramFile::find($file->id))->toBeNull();
});

it('denies delete without delete permission', function () {
    $file = TelegramFile::factory()->create();
    $viewer = User::factory()->create();
    $viewer->givePermissionTo(['system.telegram_backup.view', 'system.telegram_backup.retry']);

    $this->actingAs($viewer)
        ->delete("/system/telegram-backup/{$file->id}")
        ->assertForbidden();
});
