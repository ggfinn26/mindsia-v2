<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardWidgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardWidgetService $widgetService,
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user();
        $allWidgets = $this->widgetService->getWidgetsForUser($user, enabledOnly: false);
        $globalFilters = [
            'branch_id' => $request->query('branch_id', $user->employee?->branch_id),
            'month' => $request->query('month', now()->month),
            'year' => $request->query('year', now()->year),
            'area_id' => $request->query('area_id'),
            'scope' => $request->query('scope', 'cabang'),
        ];

        $widgetData = [];
        foreach ($allWidgets->filter(fn (array $w) => $w['is_enabled']) as $widget) {
            $key = $widget['key'];
            $widgetFilters = [
                'branch_id' => $request->query("{$key}_branch_id", $globalFilters['branch_id']),
                'month'     => $request->query("{$key}_month", $globalFilters['month']),
                'year'      => $request->query("{$key}_year", $globalFilters['year']),
                'area_id'   => $request->query("{$key}_area_id", $globalFilters['area_id']),
                'program_id'=> $request->query("{$key}_program_id", $request->query('program_id')),
                'scope'     => $request->query("{$key}_scope", $globalFilters['scope']),
                'branch_a'  => $request->query("{$key}_branch_a"),
                'branch_b'  => $request->query("{$key}_branch_b"),
                'month_start'=> $request->query("{$key}_month_start"),
                'month_end' => $request->query("{$key}_month_end"),
            ];
            $widgetData[$key] = $this->widgetService->getWidgetData($key, $widgetFilters);
        }

        return view('dashboard.index', [
            'user' => $user,
            'widgets' => $allWidgets,
            'widgetData' => $widgetData,
        ]);
    }

    public function updateWidgetOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*' => ['required', 'integer', 'min:1'],
        ]);

        $this->widgetService->updateWidgetOrder(auth()->user(), $validated['orders']);

        return response()->json(['success' => true, 'message' => 'Urutan widget berhasil diperbarui.']);
    }

    public function toggleWidget(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'widget_key' => ['required', 'string'],
            'is_enabled' => ['required', 'boolean'],
        ]);

        $this->widgetService->toggleWidget(auth()->user(), $validated['widget_key'], $validated['is_enabled']);

        return response()->json(['success' => true, 'message' => 'Widget berhasil diperbarui.']);
    }
}
