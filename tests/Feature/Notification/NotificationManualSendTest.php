<?php

use App\Models\Employee;
use App\Models\NotificationTemplate;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();
    Permission::firstOrCreate(['name' => 'notification.send_manual']);
    $this->user->givePermissionTo('notification.send_manual');
});

it('can view manual send page', function () {
    $this->actingAs($this->user)
        ->get(route('notification-manual-send.create'))
        ->assertStatus(200);
});

it('can dispatch manual notification to employee', function () {
    $template = NotificationTemplate::factory()->create([
        'template_key' => 'manual-test',
        'type' => 'in-app',
    ]);

    $employee = Employee::factory()->create();

    $data = [
        'template_key' => 'manual-test',
        'recipient_type' => 'employee',
        'recipient_id' => $employee->id,
        'payload' => ['name' => 'John'],
    ];

    $this->actingAs($this->user)
        ->post(route('notification-manual-send.store'), $data)
        ->assertRedirect(route('notification-logs.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('notification_logs', [
        'notification_template_id' => $template->id,
        'employee_id' => $employee->id,
    ]);
});
