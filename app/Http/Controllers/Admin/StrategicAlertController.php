<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Alerts\StrategicAlert;
use App\Services\Alerts\StrategicAlertService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class StrategicAlertController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:strategic_alerts.view'),
        ];
    }

    public function __construct(private readonly StrategicAlertService $alertService) {}

    public function index(Request $request): View
    {
        $branchId = $request->user()->employee?->branch_id;
        $cacheKey = 'strategic-alerts:branch:'.($branchId ?? 'all');

        $raw = cache()->remember($cacheKey, now()->addHour(), fn () => array_map(
            fn (StrategicAlert $a) => [
                'moduleKey' => $a->moduleKey,
                'moduleLabel' => $a->moduleLabel,
                'category' => $a->category,
                'subject' => $a->subject,
                'branchName' => $a->branchName,
                'detail' => $a->detail,
                'url' => $a->url,
                'severity' => $a->severity,
                'occurredAt' => $a->occurredAt?->toIso8601String(),
            ],
            $this->alertService->alertsFor($branchId)
        ));

        $alerts = array_map(fn (array $d) => new StrategicAlert(
            moduleKey: $d['moduleKey'],
            moduleLabel: $d['moduleLabel'],
            category: $d['category'],
            subject: $d['subject'],
            branchName: $d['branchName'],
            detail: $d['detail'],
            url: $d['url'],
            severity: $d['severity'],
            occurredAt: isset($d['occurredAt']) ? Carbon::parse($d['occurredAt']) : null,
        ), $raw);

        $segera = array_filter($alerts, fn ($a) => $a->severity === 'danger');
        $perhatian = array_filter($alerts, fn ($a) => $a->severity === 'warning');

        $groupedSegera = [];
        foreach ($segera as $alert) {
            $groupedSegera[$alert->moduleLabel][] = $alert;
        }

        $groupedPerhatian = [];
        foreach ($perhatian as $alert) {
            $groupedPerhatian[$alert->moduleLabel][] = $alert;
        }

        return view('admin.strategic-alert.index', [
            'groupedSegera' => $groupedSegera,
            'groupedPerhatian' => $groupedPerhatian,
            'totalSegera' => count($segera),
            'totalPerhatian' => count($perhatian),
            'total' => count($alerts),
        ]);
    }
}
