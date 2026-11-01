<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\ResendNotificationRequest;
use App\Jobs\SendAttendanceTelegramLogJob;
use App\Jobs\SendEmailNotificationJob;
use App\Jobs\SendTelegramNotificationJob;
use App\Repositories\Notification\NotificationLogRepository;
use App\Services\Notification\NotificationTemplateRendererService;
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

    public function resend(ResendNotificationRequest $request, int $id, NotificationTemplateRendererService $renderer): RedirectResponse
    {
        $log = $this->repository->findById($id);

        if (! in_array($log->notification_type, ['email', 'telegram'], true)) {
            return back()->with('error', 'Notifikasi jenis ini tidak bisa dikirim ulang.');
        }

        // Log lama tidak menyimpan isi pesan; susun ulang dari template dan variabelnya.
        $body = $log->payload['body'] ?? ($log->template ? $renderer->render($log->template, $log->payload ?? []) : '');
        $subject = $log->payload['subject'] ?? ($log->template ? $renderer->renderSubject($log->template, $log->payload ?? []) : null);

        if (trim($body) === '') {
            return back()->with('error', 'Isi pesan tidak ditemukan, tidak bisa dikirim ulang.');
        }

        match (true) {
            $log->notification_type === 'email' => dispatch(new SendEmailNotificationJob($log, $subject, $body)),
            ($log->payload['kind'] ?? null) === 'attendance_log' => dispatch(new SendAttendanceTelegramLogJob($log)),
            default => dispatch(new SendTelegramNotificationJob($log, $body)),
        };

        return back()->with('success', 'Notifikasi sedang dikirim ulang.');
    }
}
