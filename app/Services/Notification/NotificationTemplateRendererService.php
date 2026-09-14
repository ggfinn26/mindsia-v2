<?php

namespace App\Services\Notification;

use App\Models\NotificationTemplate;

class NotificationTemplateRendererService
{
    public function render(NotificationTemplate $template, array $payload): string
    {
        $body = $template->body;
        $escape = in_array($template->type, ['email', 'in-app']);

        foreach ($payload as $key => $value) {
            $safe = $escape ? e((string) $value) : (string) $value;
            $body = str_replace('{{'.$key.'}}', $safe, $body);
        }

        return $body;
    }

    public function renderSubject(NotificationTemplate $template, array $payload): ?string
    {
        if (! $template->subject) {
            return null;
        }

        $subject = $template->subject;

        foreach ($payload as $key => $value) {
            $subject = str_replace('{{'.$key.'}}', e((string) $value), $subject);
        }

        return $subject;
    }
}
