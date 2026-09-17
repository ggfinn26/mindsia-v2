<?php

use App\Models\Bot;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
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

test('it can toggle bot active status', function () {
    $bot = Bot::create(['name' => 'Test Bot', 'type' => 'telegram', 'token' => 'dummy', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->post(route('system.bots.toggle', $bot))
        ->assertRedirect(route('system.bots.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('bots', [
        'id' => $bot->id,
        'is_active' => 0,
    ]);
});
