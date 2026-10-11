<?php

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
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

test('it renders empty state when no api keys exist', function () {
    $this->actingAs($this->admin)
        ->get(route('system.api-keys.index'))
        ->assertStatus(200)
        ->assertSee('Belum ada API key yang terdaftar.');
});

test('it can check convertapi quota', function () {
    $apiKey = ApiKey::create([
        'type' => 'convertapi',
        'label' => 'ConvertAPI Key',
        'key_value' => 'dummy_secret_key',
        'is_active' => true,
    ]);

    Http::fake([
        'https://v2.convertapi.com/user' => Http::response([
            'SecondsLeft' => 1500,
            'ConversionsConsumed' => 45,
            'SubscriptionValidUntil' => '2026-12-31',
            'ActivePlan' => 'Scale',
        ], 200),
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('system.api-keys.quota', $apiKey));

    $response->assertStatus(200)
        ->assertJson([
            'remaining_quota' => 1500,
            'seconds_left' => 1500,
            'conversions_consumed' => 45,
            'reset_date' => '2026-12-31',
            'plan' => 'Scale',
        ]);
});

test('it fails check quota for non-convertapi type', function () {
    $apiKey = ApiKey::create([
        'type' => 'telegram',
        'label' => 'Telegram Bot Key',
        'key_value' => 'dummy_secret_key',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('system.api-keys.quota', $apiKey));

    $response->assertStatus(422)
        ->assertJson([
            'error' => 'Quota check tidak tersedia untuk tipe ini.',
        ]);
});

test('unauthorized user cannot manage api keys', function () {
    $regularUser = User::factory()->create();

    $this->actingAs($regularUser)
        ->get(route('system.api-keys.index'))
        ->assertForbidden();

    $this->actingAs($regularUser)
        ->post(route('system.api-keys.store'), [
            'type' => 'telegram',
            'label' => 'Hacker Key',
            'key_value' => 'secret',
        ])
        ->assertForbidden();

    $this->actingAs($regularUser)
        ->getJson(route('system.api-keys.total-quota'))
        ->assertForbidden();
});

test('it calculates total remaining quota across active convertapi keys', function () {
    ApiKey::create([
        'type' => 'convertapi',
        'label' => 'ConvertAPI Key 1',
        'key_value' => 'key_1',
        'is_active' => true,
    ]);

    ApiKey::create([
        'type' => 'convertapi',
        'label' => 'ConvertAPI Key 2',
        'key_value' => 'key_2',
        'is_active' => true,
    ]);

    ApiKey::create([
        'type' => 'convertapi',
        'label' => 'ConvertAPI Key Inactive',
        'key_value' => 'key_inactive',
        'is_active' => false,
    ]);

    Http::fake([
        'https://v2.convertapi.com/user' => Http::sequence()
            ->push(['SecondsLeft' => 1200, 'ConversionsConsumed' => 10, 'ActivePlan' => 'Scale'], 200)
            ->push(['SecondsLeft' => 800, 'ConversionsConsumed' => 25, 'ActivePlan' => 'Scale'], 200),
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('system.api-keys.total-quota'));

    $response->assertStatus(200)
        ->assertJson([
            'total_remaining_quota' => 2000,
            'total_seconds_left' => 2000,
            'total_conversions_consumed' => 35,
            'active_keys_count' => 2,
            'success_count' => 2,
            'error_count' => 0,
        ]);
});

test('it returns zero total quota when no active convertapi keys exist', function () {
    $response = $this->actingAs($this->admin)
        ->getJson(route('system.api-keys.total-quota'));

    $response->assertStatus(200)
        ->assertJson([
            'total_remaining_quota' => 0,
            'total_seconds_left' => 0,
            'total_conversions_consumed' => 0,
            'active_keys_count' => 0,
            'success_count' => 0,
        ]);
});
