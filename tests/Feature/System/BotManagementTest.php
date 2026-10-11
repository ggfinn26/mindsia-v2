<?php

use App\Models\Bot;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo('system.bot.manage');
});

test('it can view bots list', function () {
    $bot = Bot::create(['name' => 'Test Bot', 'type' => 'telegram', 'token' => 'dummy', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->get(route('system.bots.index'))
        ->assertStatus(200);
});

test('it can view create bot form', function () {
    $this->actingAs($this->admin)
        ->get(route('system.bots.create'))
        ->assertStatus(200);
});

test('it can store a new bot', function () {
    $data = [
        'name' => 'Telegram Notifier',
        'type' => 'telegram',
        'token' => 'dummy_token_123',
        'is_active' => true,
        'notes' => 'For system alerts',
    ];

    $this->actingAs($this->admin)
        ->post(route('system.bots.store'), $data)
        ->assertRedirect(route('system.bots.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('bots', [
        'name' => 'Telegram Notifier',
        'type' => 'telegram',
    ]);
});

test('it can view edit bot form', function () {
    $bot = Bot::create(['name' => 'Test Bot', 'type' => 'telegram', 'token' => 'dummy', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->get(route('system.bots.edit', $bot))
        ->assertStatus(200);
});

test('it can update a bot', function () {
    $bot = Bot::create(['name' => 'Test Bot', 'type' => 'telegram', 'token' => 'dummy', 'is_active' => true]);

    $data = [
        'name' => 'Updated Bot',
        'type' => 'whatsapp',
        'token' => 'new_token_456',
        'is_active' => false,
    ];

    $this->actingAs($this->admin)
        ->put(route('system.bots.update', $bot), $data)
        ->assertRedirect(route('system.bots.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('bots', [
        'id' => $bot->id,
        'name' => 'Updated Bot',
        'type' => 'whatsapp',
        'is_active' => 0,
    ]);
});

test('it renders empty state when no bots exist', function () {
    $this->actingAs($this->admin)
        ->get(route('system.bots.index'))
        ->assertStatus(200)
        ->assertSee('Belum ada konfigurasi bot.');
});

test('it can toggle bot active status from active to inactive', function () {
    $bot = Bot::create(['name' => 'Active Bot', 'type' => 'telegram', 'token' => 'dummy', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->post(route('system.bots.toggle', $bot))
        ->assertRedirect(route('system.bots.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('bots', [
        'id' => $bot->id,
        'is_active' => 0,
    ]);
});

test('it toggles inactive bot to active', function () {
    $bot = Bot::create(['name' => 'Inactive Bot', 'type' => 'whatsapp', 'token' => 'dummy', 'is_active' => false]);

    $this->actingAs($this->admin)
        ->post(route('system.bots.toggle', $bot))
        ->assertRedirect(route('system.bots.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('bots', [
        'id' => $bot->id,
        'is_active' => 1,
    ]);
});

test('it fails validation when storing bot with invalid data', function () {
    $this->actingAs($this->admin)
        ->post(route('system.bots.store'), [
            'name' => '',
            'type' => 'invalid_platform',
            'token' => '',
        ])
        ->assertSessionHasErrors(['name', 'type', 'token']);
});

test('unauthorized user without system.bot.manage sees read-only view without action buttons and cannot store or update', function () {
    $regularUser = User::factory()->create();
    $bot = Bot::create(['name' => 'Existing Bot', 'type' => 'telegram', 'token' => 'dummy', 'is_active' => true]);

    $this->actingAs($regularUser)
        ->get(route('system.bots.index'))
        ->assertStatus(200)
        ->assertDontSee('Tambah Bot')
        ->assertDontSee('Nonaktifkan');

    $this->actingAs($regularUser)
        ->post(route('system.bots.store'), [
            'name' => 'Hacker Bot',
            'type' => 'telegram',
            'token' => 'secret',
        ])
        ->assertForbidden();

    $this->actingAs($regularUser)
        ->put(route('system.bots.update', $bot), [
            'name' => 'Hacker Bot',
        ])
        ->assertForbidden();
});
