<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemAccess\StoreNotificationRoutingRequest;
use App\Http\Requests\SystemAccess\UpdateNotificationRoutingRequest;
use App\Models\NotificationRouting;
use App\Repositories\PositionRepository;
use App\Repositories\SystemAccess\NotificationRoutingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationRoutingController extends Controller
{
    public function __construct(
        private readonly NotificationRoutingRepository $repository,
        private readonly PositionRepository $positionRepository,
    ) {}

    public function index(): View
    {
        return view('system-access.notification-routing.index', [
            'routings' => $this->repository->all(),
        ]);
    }

    public function create(): View
    {
        return view('system-access.notification-routing.create', [
            'positions' => $this->positionRepository->all(),
        ]);
    }

    public function store(StoreNotificationRoutingRequest $request): RedirectResponse
    {
        $this->repository->create($request->validated());

        return redirect()->route('system.notification-routings.index')->with('success', 'Routing notifikasi berhasil disimpan.');
    }

    public function edit(NotificationRouting $notificationRouting): View
    {
        return view('system-access.notification-routing.edit', [
            'routing' => $notificationRouting,
            'positions' => $this->positionRepository->all(),
        ]);
    }

    public function update(UpdateNotificationRoutingRequest $request, NotificationRouting $notificationRouting): RedirectResponse
    {
        $this->repository->update($notificationRouting, $request->validated());

        return redirect()->route('system.notification-routings.index')->with('success', 'Routing berhasil diperbarui.');
    }

    public function destroy(NotificationRouting $notificationRouting): RedirectResponse
    {
        $this->repository->delete($notificationRouting);

        return redirect()->route('system.notification-routings.index')->with('success', 'Routing berhasil dihapus.');
    }
}
