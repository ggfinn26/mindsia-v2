<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\ManualSendNotificationRequest;
use App\Models\Employee;
use App\Models\MemberData;
use App\Repositories\Notification\NotificationTemplateRepository;
use App\Services\Notification\NotificationDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationManualSendController extends Controller
{
    public function __construct(
        private readonly NotificationTemplateRepository $templateRepository,
        private readonly NotificationDispatchService $dispatcher,
    ) {}

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('notification.send_manual'), 403);

        return view('notification.manual-send.create', [
            'templates' => $this->templateRepository->all(),
        ]);
    }

    public function store(ManualSendNotificationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $recipient = $data['recipient_type'] === 'employee'
            ? Employee::findOrFail($data['recipient_id'])
            : MemberData::findOrFail($data['recipient_id']);

        $this->dispatcher->send($data['template_key'], $recipient, $data['payload'] ?? []);

        return redirect()->route('notification-logs.index')->with('success', 'Notifikasi berhasil dikirim.');
    }
}
