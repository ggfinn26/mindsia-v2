<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Enums\WidgetKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemAccess\StoreDashboardWidgetConfigRequest;
use App\Http\Requests\SystemAccess\UpdateDashboardWidgetConfigRequest;
use App\Models\DashboardWidgetConfig;
use App\Models\Position;
use App\Repositories\PositionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardWidgetConfigController extends Controller
{
    public function __construct(
        private readonly PositionRepository $positionRepository,
    ) {}

    public function index(): View
    {
        $configs = DashboardWidgetConfig::with('position')->get();

        // Group by widget_key → collect position configs per widget
        $byWidget = $configs->groupBy('widget_key');

        $widgetKeys = collect(WidgetKey::cases())
            ->groupBy(fn (WidgetKey $wk) => $wk->category());

        $positions = Position::with(['role', 'dashboardWidgetConfigs'])
            ->orderBy('position_name')
            ->get();

        return view('system-access.dashboard-widget.index', [
            'byWidget' => $byWidget,
            'widgetKeys' => $widgetKeys,
            'positions' => $positions,
        ]);
    }

    public function editPosition(Position $position): View
    {
        $position->load(['role', 'dashboardWidgetConfigs']);

        $widgetKeys = collect(WidgetKey::cases())
            ->groupBy(fn (WidgetKey $wk) => $wk->category());

        $assignedConfigs = $position->dashboardWidgetConfigs->keyBy('widget_key');

        return view('system-access.dashboard-widget.edit-position', [
            'position' => $position,
            'widgetKeys' => $widgetKeys,
            'assignedConfigs' => $assignedConfigs,
        ]);
    }

    public function updatePosition(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'widgets' => ['nullable', 'array'],
            'widgets.*.enabled' => ['nullable', 'boolean'],
            'widgets.*.order' => ['nullable', 'integer', 'min:1'],
        ]);

        DashboardWidgetConfig::where('position_id', $position->id)->delete();

        foreach ($validated['widgets'] ?? [] as $widgetKey => $data) {
            if (! empty($data['enabled'])) {
                DashboardWidgetConfig::create([
                    'position_id' => $position->id,
                    'widget_key' => $widgetKey,
                    'order' => isset($data['order']) && $data['order'] !== '' ? (int) $data['order'] : 1,
                    'is_enabled' => true,
                ]);
            }
        }

        return redirect()
            ->route('system.dashboard-widgets.index')
            ->with('success', 'Konfigurasi widget dashboard untuk posisi "'.$position->position_name.'" berhasil diperbarui.');
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

    public function assignPositions(Request $request, string $widgetKey): RedirectResponse
    {
        $wk = WidgetKey::tryFrom($widgetKey);
        abort_if(! $wk, 404);

        $validated = $request->validate([
            'positions' => ['nullable', 'array'],
            'positions.*.position_id' => ['required', 'exists:positions,id'],
            'positions.*.order' => ['required', 'integer', 'min:1'],
            'positions.*.is_enabled' => ['nullable', 'boolean'],
        ]);

        DashboardWidgetConfig::where('widget_key', $widgetKey)->delete();

        foreach ($validated['positions'] ?? [] as $row) {
            DashboardWidgetConfig::create([
                'widget_key' => $widgetKey,
                'position_id' => $row['position_id'],
                'order' => $row['order'],
                'is_enabled' => isset($row['is_enabled']) ? (bool) $row['is_enabled'] : true,
            ]);
        }

        return redirect()->route('system.dashboard-widgets.index')
            ->with('success', 'Konfigurasi widget "'.$wk->label().'" berhasil diperbarui.');
    }
}
