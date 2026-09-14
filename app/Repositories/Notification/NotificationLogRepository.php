<?php

namespace App\Repositories\Notification;

use App\Models\NotificationLog;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationLogRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return NotificationLog::with('template', 'employee', 'member')
            ->when(! empty($filters['status']), fn ($q) => $q->where('notification_status', $filters['status']))
            ->when(! empty($filters['type']), fn ($q) => $q->where('notification_type', $filters['type']))
            ->when(! empty($filters['employee_id']), fn ($q) => $q->where('employee_id', $filters['employee_id']))
            ->when(! empty($filters['member_id']), fn ($q) => $q->where('member_id', $filters['member_id']))
            ->orderByDesc('created_at')
            ->paginate(50);
    }

    public function findById(int $id): NotificationLog
    {
        return NotificationLog::findOrFail($id);
    }

    public function create(array $data): NotificationLog
    {
        return NotificationLog::create($data);
    }

    public function markSent(NotificationLog $log): void
    {
        $log->update([
            'notification_status' => 'sent',
            'notification_sent_at' => now(),
            'last_attempt_at' => now(),
            'attempts' => $log->attempts + 1,
            'notification_error' => null,
        ]);
    }

    public function markFailed(NotificationLog $log, string $error): void
    {
        $log->update([
            'notification_status' => 'failed',
            'last_attempt_at' => now(),
            'attempts' => $log->attempts + 1,
            'notification_error' => $error,
        ]);
    }
}
