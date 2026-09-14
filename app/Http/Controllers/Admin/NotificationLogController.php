<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\ResendNotificationRequest;
use App\Jobs\SendEmailNotificationJob;
use App\Jobs\SendTelegramNotificationJob;
use App\Repositories\Notification\NotificationLogRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationLogController extends Controller
{
    public function __construct(
        private readonly NotificationLogRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('notification.log.view'), 403);

        return view('notification.log.index', [
            'logs' => $this->repository->paginate($request->only(['status', 'type', 'employee_id', 'member_id'])),
        ]);
    }

    public function resend(ResendNotificationRequest $request, int $id): RedirectResponse
    {
        $log = $this->repository->findById($id);

        match ($log->notification_type) {
            'email' => dispatch(new SendEmailNotificationJob($log, null, $log->payload['body'] ?? '')),
            'telegram' => dispatch(new SendTelegramNotificationJob($log, $log->payload['body'] ?? '')),
            default => null,
        };

        return back()->with('success', 'Notifikasi sedang dikirim ulang.');
    }
}
