<?php

use App\Jobs\SendEmailNotificationJob;
use App\Jobs\SendTelegramNotificationJob;
use App\Models\Employee;
use App\Models\MemberData;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Services\Notification\NotificationDispatchService;
use Illuminate\Support\Facades\Queue;

it('dispatches in-app notification', function () {
    $service = app(NotificationDispatchService::class);
    $template = NotificationTemplate::factory()->create([
        'template_key' => 'test-in-app',
        'type' => 'in-app',
        'subject' => 'Hello {{name}}',
        'body' => 'Welcome {{name}}',
    ]);

    $employee = Employee::factory()->create();

    $service->send('test-in-app', $employee, ['name' => 'John Doe']);

    $this->assertDatabaseHas('employee_notifications', [
        'employee_id' => $employee->id,
        'subject' => 'Hello John Doe',
        'message' => 'Welcome John Doe',
        'status' => 'unread',
    ]);

    $this->assertDatabaseHas('notification_logs', [
        'notification_template_id' => $template->id,
        'employee_id' => $employee->id,
        'notification_type' => 'in-app',
        'notification_status' => 'sent',
    ]);
});

it('dispatches email notification job', function () {
    Queue::fake();

    $service = app(NotificationDispatchService::class);
    $template = NotificationTemplate::factory()->create([
        'template_key' => 'test-email',
        'type' => 'email',
        'subject' => 'Hello {{name}}',
        'body' => 'Welcome {{name}}',
    ]);

    $member = MemberData::factory()->create(['email' => 'test@example.com']);

    $service->send('test-email', $member, ['name' => 'Jane Doe']);

    Queue::assertPushed(SendEmailNotificationJob::class);

    $this->assertDatabaseHas('notification_logs', [
        'notification_template_id' => $template->id,
        'member_id' => $member->id,
        'notification_type' => 'email',
        'channel' => 'test@example.com',
        'notification_status' => 'pending',
    ]);
});

it('dispatches telegram notification job', function () {
    Queue::fake();

    $service = app(NotificationDispatchService::class);
    $template = NotificationTemplate::factory()->create([
        'template_key' => 'test-telegram',
        'type' => 'telegram',
        'subject' => 'Subject',
        'body' => 'Welcome {{name}}',
    ]);

    $employee = Employee::factory()->create();
    $user = User::factory()->create([
        'employee_id' => $employee->id,
        'telegram_chat_id' => '123456789',
    ]);
    // Refresh employee to load relation or ensure accessor works
    $employee->load('user');

    $service->send('test-telegram', $employee, ['name' => 'Alice']);

    Queue::assertPushed(SendTelegramNotificationJob::class);

    $this->assertDatabaseHas('notification_logs', [
        'notification_template_id' => $template->id,
        'employee_id' => $employee->id,
        'notification_type' => 'telegram',
        'channel' => '123456789',
        'notification_status' => 'pending',
    ]);
});

it('can send email directly to an address', function () {
    Queue::fake();

    $service = app(NotificationDispatchService::class);
    $template = NotificationTemplate::factory()->create([
        'template_key' => 'test-email-direct',
        'type' => 'email',
        'subject' => 'Hello {{name}}',
        'body' => 'Welcome {{name}}',
    ]);

    $service->sendToEmail('applicant@example.com', 'test-email-direct', ['name' => 'Applicant']);

    Queue::assertPushed(SendEmailNotificationJob::class);

    $this->assertDatabaseHas('notification_logs', [
        'notification_template_id' => $template->id,
        'employee_id' => null,
        'member_id' => null,
        'notification_type' => 'email',
        'channel' => 'applicant@example.com',
        'notification_status' => 'pending',
    ]);
});
