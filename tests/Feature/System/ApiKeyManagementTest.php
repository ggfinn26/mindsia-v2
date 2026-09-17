<?php

use App\Models\ApiKey;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo([
        'system.api_key.view',
        'system.api_key.create',
        'system.api_key.update',
        'system.api_key.delete',
    ]);
});

test('it can view api keys list', function () {
    $apiKey = ApiKey::create(['type' => 'wa', 'label' => 'Test Key', 'key_value' => 'dummy', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->get(route('system.api-keys.index'))
        ->assertStatus(200);
});

test('it can view create api key form', function () {
    $this->actingAs($this->admin)
        ->get(route('system.api-keys.create'))
        ->assertStatus(200);
});

test('it can store a new api key', function () {
    $data = [
        'type' => 'telegram',
        'label' => 'New Key',
        'key_value' => 'dummy_key_123',
        'is_active' => true,
        'notes' => 'For system alerts',
    ];

    $this->actingAs($this->admin)
        ->post(route('system.api-keys.store'), $data)
        ->assertRedirect(route('system.api-keys.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('api_keys', [
        'label' => 'New Key',
        'type' => 'telegram',
    ]);
});

test('it can view edit api key form', function () {
    $apiKey = ApiKey::create(['type' => 'wa', 'label' => 'Test Key', 'key_value' => 'dummy', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->get(route('system.api-keys.edit', $apiKey))
        ->assertStatus(200);
});

test('it can update an api key', function () {
    $apiKey = ApiKey::create(['type' => 'wa', 'label' => 'Test Key', 'key_value' => 'dummy', 'is_active' => true]);

    $data = [
        'type' => 'telegram',
        'label' => 'Updated Key',
        'key_value' => 'new_key_456',
        'is_active' => false,
    ];

    $this->actingAs($this->admin)
        ->put(route('system.api-keys.update', $apiKey), $data)
        ->assertRedirect(route('system.api-keys.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('api_keys', [
        'id' => $apiKey->id,
        'label' => 'Updated Key',
        'type' => 'telegram',
        'is_active' => 0,
    ]);
});

test('it can toggle api key active status', function () {
    $apiKey = ApiKey::create(['type' => 'wa', 'label' => 'Test Key', 'key_value' => 'dummy', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->post(route('system.api-keys.toggle', $apiKey))
        ->assertRedirect(route('system.api-keys.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('api_keys', [
        'id' => $apiKey->id,
        'is_active' => 0,
    ]);
});

test('it can delete an api key', function () {
    $apiKey = ApiKey::create(['type' => 'wa', 'label' => 'Test Key', 'key_value' => 'dummy', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->delete(route('system.api-keys.destroy', $apiKey))
        ->assertRedirect(route('system.api-keys.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('api_keys', [
        'id' => $apiKey->id,
    ]);
});
