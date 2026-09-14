<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\StoreNotificationTemplateRequest;
use App\Http\Requests\Notification\StoreNotificationVariableRequest;
use App\Http\Requests\Notification\UpdateNotificationTemplateRequest;
use App\Http\Requests\Notification\UpdateNotificationVariableRequest;
use App\Models\NotificationTemplate;
use App\Models\NotificationVariable;
use App\Repositories\Notification\NotificationTemplateRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationTemplateController extends Controller
{
    public function __construct(
        private readonly NotificationTemplateRepository $repository,
    ) {}

    public function index(): View
    {
        return view('notification.template.index', [
            'templates' => $this->repository->all(),
        ]);
    }

    public function create(): View
    {
        return view('notification.template.create');
    }

    public function store(StoreNotificationTemplateRequest $request): RedirectResponse
    {
        $this->repository->create($request->validated());

        return redirect()->route('notification-templates.index')->with('success', 'Template berhasil ditambahkan.');
    }

    public function show(NotificationTemplate $notificationTemplate): View
    {
        return view('notification.template.show', [
            'template' => $this->repository->find($notificationTemplate->id),
        ]);
    }

    public function edit(NotificationTemplate $notificationTemplate): View
    {
        return view('notification.template.edit', compact('notificationTemplate'));
    }

    public function update(UpdateNotificationTemplateRequest $request, NotificationTemplate $notificationTemplate): RedirectResponse
    {
        $this->repository->update($notificationTemplate, $request->validated());

        return redirect()->route('notification-templates.show', $notificationTemplate)->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(Request $request, NotificationTemplate $notificationTemplate): RedirectResponse
    {
        abort_unless($request->user()->can('notification.template.delete'), 403);

        $this->repository->delete($notificationTemplate);

        return redirect()->route('notification-templates.index')->with('success', 'Template berhasil dihapus.');
    }

    public function storeVariable(StoreNotificationVariableRequest $request, NotificationTemplate $notificationTemplate): RedirectResponse
    {
        $this->repository->addVariable($notificationTemplate, $request->validated());

        return redirect()->route('notification-templates.show', $notificationTemplate)->with('success', 'Variable berhasil ditambahkan.');
    }

    public function updateVariable(UpdateNotificationVariableRequest $request, NotificationTemplate $notificationTemplate, NotificationVariable $variable): RedirectResponse
    {
        $this->repository->updateVariable($variable, $request->validated());

        return redirect()->route('notification-templates.show', $notificationTemplate)->with('success', 'Variable berhasil diperbarui.');
    }

    public function destroyVariable(Request $request, NotificationTemplate $notificationTemplate, NotificationVariable $variable): RedirectResponse
    {
        abort_unless($request->user()->can('notification.variable.delete'), 403);
        abort_unless($variable->notification_template_id === $notificationTemplate->id, 404);

        $this->repository->deleteVariable($variable);

        return redirect()->route('notification-templates.show', $notificationTemplate)->with('success', 'Variable dihapus.');
    }
}
