<?php

use App\Models\NotificationRouting;
use App\Models\Position;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo('system.notification_routing.manage');
});

test('it can view notification routings list', function () {
    $routing = NotificationRouting::create(['event_key' => 'test_event', 'channel' => 'telegram', 'scope' => 'global', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->get(route('system.notification-routings.index'))
        ->assertStatus(200);
});

test('it can view create notification routing form', function () {
    $this->actingAs($this->admin)
        ->get(route('system.notification-routings.create'))
        ->assertStatus(200);
});

test('it can store a new notification routing', function () {
    $position = Position::create(['position_name' => 'HR']);

    $data = [
        'event_key' => 'employee_hired',
        'position_id' => $position->id,
        'channel' => 'telegram',
        'scope' => 'branch',
        'is_active' => true,
        'description' => 'Notifikasi karyawan baru',
    ];

    $this->actingAs($this->admin)
        ->post(route('system.notification-routings.store'), $data)
        ->assertRedirect(route('system.notification-routings.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('notification_routings', [
        'event_key' => 'employee_hired',
        'channel' => 'telegram',
    ]);
});

test('it can view edit notification routing form', function () {
    $routing = NotificationRouting::create(['event_key' => 'test_event', 'channel' => 'telegram', 'scope' => 'global', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->get(route('system.notification-routings.edit', $routing))
        ->assertStatus(200);
});

test('it can update a notification routing', function () {
    $routing = NotificationRouting::create(['event_key' => 'test_event', 'channel' => 'telegram', 'scope' => 'global', 'is_active' => true]);

    $data = [
        'event_key' => 'employee_fired',
        'channel' => 'email',
        'scope' => 'global',
        'is_active' => false,
    ];

    $this->actingAs($this->admin)
        ->put(route('system.notification-routings.update', $routing), $data)
        ->assertRedirect(route('system.notification-routings.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('notification_routings', [
        'id' => $routing->id,
        'event_key' => 'employee_fired',
        'channel' => 'email',
        'is_active' => 0,
    ]);
});

test('it can delete a notification routing', function () {
    $routing = NotificationRouting::create(['event_key' => 'test_event', 'channel' => 'telegram', 'scope' => 'global', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->delete(route('system.notification-routings.destroy', $routing))
        ->assertRedirect(route('system.notification-routings.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('notification_routings', [
        'id' => $routing->id,
    ]);
});
