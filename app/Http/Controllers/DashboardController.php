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

    public function index(): View
    {
        $user = auth()->user();
        $allWidgets = $this->widgetService->getWidgetsForUser($user, enabledOnly: false);
        $branchId = $user->employee?->branch_id;

        $widgetData = [];
        foreach ($allWidgets->filter(fn (array $w) => $w['is_enabled']) as $widget) {
            $widgetData[$widget['key']] = $this->widgetService->getWidgetData($widget['key'], $branchId);
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
