<?php

use App\Jobs\SendEmailNotificationJob;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();
    Permission::firstOrCreate(['name' => 'notification.log.view']);
    Permission::firstOrCreate(['name' => 'notification.log.resend']);
    $this->user->givePermissionTo(['notification.log.view', 'notification.log.resend']);
});

it('can view notification logs', function () {
    $template = NotificationTemplate::factory()->create();
    NotificationLog::factory()->create([
        'notification_template_id' => $template->id,
        'notification_type' => 'email',
        'channel' => 'test@example.com',
    ]);

    $this->actingAs($this->user)
        ->get(route('notification-logs.index'))
        ->assertStatus(200);

});

it('can resend failed notification', function () {
    Queue::fake();

    $template = NotificationTemplate::factory()->create();
    $log = NotificationLog::factory()->create([
        'notification_template_id' => $template->id,
        'notification_type' => 'email',
        'channel' => 'test@example.com',
        'payload' => ['body' => 'Test body'],
    ]);

    $this->actingAs($this->user)
        ->post(route('notification-logs.resend', $log->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    Queue::assertPushed(SendEmailNotificationJob::class);
});
