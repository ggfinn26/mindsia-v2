<?php

namespace App\Services\Dashboard\Widgets;

use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class SystemAuditLogWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'system_audit_log';
    }

    public function getData(array $filters = []): array
    {
        return [
            'recent_logs' => [],
            'today_count' => 0,
            'by_type' => [],
            'period' => Carbon::now()->translatedFormat('F Y'),
        ];
    }

    public function getExportData(array $filters = []): array
    {
        return [];
    }
}
