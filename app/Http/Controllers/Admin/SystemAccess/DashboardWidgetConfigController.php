<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemAccess\StoreDashboardWidgetConfigRequest;
use App\Http\Requests\SystemAccess\UpdateDashboardWidgetConfigRequest;
use App\Models\DashboardWidgetConfig;
use App\Repositories\PositionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardWidgetConfigController extends Controller
{
    public function __construct(
        private readonly PositionRepository $positionRepository,
    ) {}

    public function index(): View
    {
        $configs = DashboardWidgetConfig::with('position')
            ->orderBy('position_id')
            ->orderBy('order')
            ->get()
            ->groupBy('position_id');

        return view('system-access.dashboard-widget.index', [
            'configs' => $configs,
            'positions' => $this->positionRepository->all(),
        ]);
    }

    public function store(StoreDashboardWidgetConfigRequest $request): RedirectResponse
    {
        DashboardWidgetConfig::create($request->validated());

        return redirect()->route('system.dashboard-widgets.index')->with('success', 'Widget berhasil ditambahkan.');
    }

    public function update(UpdateDashboardWidgetConfigRequest $request, DashboardWidgetConfig $dashboardWidgetConfig): RedirectResponse
    {
        $dashboardWidgetConfig->update($request->validated());

        return redirect()->route('system.dashboard-widgets.index')->with('success', 'Widget berhasil diperbarui.');
    }

    public function destroy(DashboardWidgetConfig $dashboardWidgetConfig): RedirectResponse
    {
        $dashboardWidgetConfig->delete();

        return redirect()->route('system.dashboard-widgets.index')->with('success', 'Widget berhasil dihapus.');
    }
}
