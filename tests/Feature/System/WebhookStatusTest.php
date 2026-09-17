<?php

use App\Models\Webhook;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo('system.webhook.manage');
});

test('it can view webhooks list', function () {
    $webhook = Webhook::create(['name' => 'Test Webhook', 'endpoint_url' => 'https://example.com/webhook']);

    $this->actingAs($this->admin)
        ->get(route('system.webhooks.index'))
        ->assertStatus(200);
});

test('it can view create webhook form', function () {
    $this->actingAs($this->admin)
        ->get(route('system.webhooks.create'))
        ->assertStatus(200);
});

test('it can store a new webhook', function () {
    $data = [
        'name' => 'New Webhook',
        'endpoint_url' => 'https://example.com/webhook',
        'notes' => 'Some notes',
    ];

    $this->actingAs($this->admin)
        ->post(route('system.webhooks.store'), $data)
        ->assertRedirect(route('system.webhooks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('webhooks', [
        'name' => 'New Webhook',
        'endpoint_url' => 'https://example.com/webhook',
    ]);
});

test('it fails to store webhook with internal IP', function () {
    $data = [
        'name' => 'Bad Webhook',
        'endpoint_url' => 'http://127.0.0.1/webhook',
    ];

    $this->actingAs($this->admin)
        ->post(route('system.webhooks.store'), $data)
        ->assertSessionHasErrors('endpoint_url');
});

test('it can view edit webhook form', function () {
    $webhook = Webhook::create(['name' => 'Test Webhook', 'endpoint_url' => 'https://example.com/webhook']);

    $this->actingAs($this->admin)
        ->get(route('system.webhooks.edit', $webhook))
        ->assertStatus(200);
});

test('it can update a webhook', function () {
    $webhook = Webhook::create(['name' => 'Test Webhook', 'endpoint_url' => 'https://example.com/webhook']);

    $data = [
        'name' => 'Updated Webhook',
        'endpoint_url' => 'https://example.org/webhook',
    ];

    $this->actingAs($this->admin)
        ->put(route('system.webhooks.update', $webhook), $data)
        ->assertRedirect(route('system.webhooks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('webhooks', [
        'id' => $webhook->id,
        'name' => 'Updated Webhook',
        'endpoint_url' => 'https://example.org/webhook',
    ]);
});

test('it can delete a webhook', function () {
    $webhook = Webhook::create(['name' => 'Test Webhook', 'endpoint_url' => 'https://example.com/webhook']);

    $this->actingAs($this->admin)
        ->delete(route('system.webhooks.destroy', $webhook))
        ->assertRedirect(route('system.webhooks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('webhooks', [
        'id' => $webhook->id,
    ]);
});

test('it can ping a webhook', function () {
    $webhook = Webhook::create(['name' => 'Test Webhook', 'endpoint_url' => 'https://example.com/webhook', 'last_status' => 'unknown']);

    Http::fake([
        'https://example.com/*' => Http::response('ok', 200),
    ]);

    $this->actingAs($this->admin)
        ->post(route('system.webhooks.ping', $webhook))
        ->assertRedirect(route('system.webhooks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('webhooks', [
        'id' => $webhook->id,
        'last_status' => 'ok',
    ]);
});
