<?php

namespace App\Services\Notification;

use App\Jobs\SendEmailNotificationJob;
use App\Jobs\SendTelegramNotificationJob;
use App\Models\Employee;
use App\Models\MemberData;
use App\Repositories\Notification\NotificationLogRepository;
use App\Repositories\Notification\NotificationTemplateRepository;

class NotificationDispatchService
{
    public function __construct(
        private readonly NotificationTemplateRepository $templateRepository,
        private readonly NotificationLogRepository $logRepository,
        private readonly NotificationTemplateRendererService $renderer,
    ) {}

    public function send(string $templateKey, Employee|MemberData $recipient, array $payload): void
    {
        $template = $this->templateRepository->findByKey($templateKey);

        if (! $template) {
            return;
        }

        $renderedBody = $this->renderer->render($template, $payload);
        $renderedSubject = $this->renderer->renderSubject($template, $payload);

        $isEmployee = $recipient instanceof Employee;
        $channel = $this->resolveChannel($template->type, $recipient);

        $log = $this->logRepository->create([
            'notification_template_id' => $template->id,
            'employee_id' => $isEmployee ? $recipient->id : null,
            'member_id' => $isEmployee ? null : $recipient->id,
            'notification_type' => $template->type,
            'channel' => $channel ?? 'unknown',
            'payload' => $payload,
            'notification_status' => 'pending',
            'attempts' => 0,
        ]);

        match ($template->type) {
            'in-app' => $this->sendInApp($recipient, $renderedSubject ?? '', $renderedBody, $log),
            'email' => dispatch(new SendEmailNotificationJob($log, $renderedSubject, $renderedBody)),
            'telegram' => dispatch(new SendTelegramNotificationJob($log, $renderedBody)),
            default => null,
        };
    }

    /**
     * Send email notification directly to an address (e.g. applicant).
     * Creates a log entry with no employee/member FK.
     */
    public function sendToEmail(string $emailAddress, string $templateKey, array $payload): void
    {
        $template = $this->templateRepository->findByKey($templateKey);

        if (! $template) {
            return;
        }

        $renderedBody = $this->renderer->render($template, $payload);
        $renderedSubject = $this->renderer->renderSubject($template, $payload);

        $log = $this->logRepository->create([
            'notification_template_id' => $template->id,
            'employee_id' => null,
            'member_id' => null,
            'notification_type' => 'email',
            'channel' => $emailAddress,
            'payload' => $payload,
            'notification_status' => 'pending',
            'attempts' => 0,
        ]);

        dispatch(new SendEmailNotificationJob($log, $renderedSubject, $renderedBody));
    }

    private function sendInApp(Employee|MemberData $recipient, string $subject, string $body, $log): void
    {
        $recipient->notifications()->create([
            'subject' => $subject,
            'message' => $body,
            'status' => 'unread',
        ]);

        $this->logRepository->markSent($log);
    }

    private function resolveChannel(string $type, Employee|MemberData $recipient): ?string
    {
        return match ($type) {
            'in-app' => 'in-app',
            'email' => $recipient->email ?? null,
            'telegram' => $recipient instanceof Employee
                ? ($recipient->telegram_chat_id ?? null)
                : null,
            default => null,
        };
    }
}
