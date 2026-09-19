<?php

use App\Models\NotificationTemplate;
use App\Models\NotificationVariable;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();
    $permissions = [
        'notification.template.view',
        'notification.template.create',
        'notification.template.update',
        'notification.template.delete',
        'notification.variable.create',
        'notification.variable.delete',
    ];
    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
        $this->user->givePermissionTo($perm);
    }
});

it('can list notification templates', function () {
    NotificationTemplate::factory()->create(['template_key' => 'template-a']);

    $this->actingAs($this->user)
        ->get(route('notification-templates.index'))
        ->assertStatus(200);

});

it('can store a notification template', function () {
    $data = [
        'template_key' => 'new-template',
        'type' => 'email',
        'subject' => 'Hello',
        'body' => 'Body',
    ];

    $this->actingAs($this->user)
        ->post(route('notification-templates.store'), $data)
        ->assertRedirect(route('notification-templates.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('notification_templates', ['template_key' => 'new-template']);
});

it('can update a notification template', function () {
    $template = NotificationTemplate::factory()->create(['template_key' => 'old-template', 'type' => 'email', 'subject' => 'old', 'body' => 'old']);

    $data = [
        'template_key' => $template->template_key,
        'type' => $template->type,
        'subject' => 'updated subject',
        'body' => 'Updated Body',
    ];

    $this->actingAs($this->user)
        ->put(route('notification-templates.update', $template), $data)
        ->assertRedirect(route('notification-templates.show', $template))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('notification_templates', ['body' => 'Updated Body']);
});

it('can delete a notification template', function () {
    $template = NotificationTemplate::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('notification-templates.destroy', $template))
        ->assertRedirect(route('notification-templates.index'))
        ->assertSessionHas('success');

    $this->assertModelMissing($template); // It seems there's no soft delete in the schema
});

it('can add a variable to a template', function () {
    $template = NotificationTemplate::factory()->create();

    $data = [
        'variable_name' => 'name',
        'variable_type' => 'string',
        'variable_description' => 'User Name',
    ];

    $this->actingAs($this->user)
        ->post(route('notification-templates.variables.store', $template), $data)
        ->assertRedirect(route('notification-templates.show', $template))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('notification_variables', [
        'notification_template_id' => $template->id,
        'variable_name' => 'name',
    ]);
});

it('can delete a variable from a template', function () {
    $template = NotificationTemplate::factory()->create();
    $variable = NotificationVariable::factory()->create([
        'notification_template_id' => $template->id,
        'variable_name' => 'name',
        'variable_type' => 'string',
        'variable_description' => 'Desc',
    ]);

    $this->actingAs($this->user)
        ->delete(route('notification-templates.variables.destroy', [$template, $variable]))
        ->assertRedirect(route('notification-templates.show', $template))
        ->assertSessionHas('success');

    $this->assertModelMissing($variable);
});
