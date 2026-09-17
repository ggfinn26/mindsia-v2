<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardWidgetService;
use Illuminate\Http\RedirectResponse;
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
        $widgets = $this->widgetService->getWidgetsForUser($user);
        $branchId = $user->employee?->branch_id;

        $widgetData = [];
        foreach ($widgets as $widget) {
            $widgetData[$widget['key']] = $this->widgetService->getWidgetData($widget['key'], $branchId);
        }

        return view('dashboard.index', [
            'user' => $user,
            'widgets' => $widgets,
            'widgetData' => $widgetData,
        ]);
    }

    public function updateWidgetOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*' => ['required', 'string', 'min:1'],
        ]);

        $this->widgetService->updateWidgetOrder(auth()->user(), $validated['orders']);

        return back()->with('success', 'Urutan widget berhasil diperbarui.');
    }

    public function toggleWidget(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'widget_key' => ['required', 'string'],
            'is_enabled' => ['required', 'boolean'],
        ]);

        $this->widgetService->toggleWidget(auth()->user(), $validated['widget_key'], $validated['is_enabled']);

        return back()->with('success', 'Widget berhasil diperbarui.');
    }
}
