<?php

namespace App\Services\Dashboard\Widgets;

use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SystemAuditLogWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'system_audit_log';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        // Activity log — using DB::table if no dedicated audit model
        $recentLogs = DB::table('activity_log')
            ->latest('created_at')
            ->limit(20)
            ->get(['id', 'log_name', 'description', 'subject_type', 'causer_type', 'created_at'])
            ->map(fn ($log) => [
                'description' => $log->description,
                'subject' => class_basename($log->subject_type ?? ''),
                'causer' => class_basename($log->causer_type ?? ''),
                'created_at' => $log->created_at,
            ])->toArray();

        $todayCount = DB::table('activity_log')
            ->whereDate('created_at', $now->toDateString())
            ->count();

        $byLogName = DB::table('activity_log')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->selectRaw('log_name, COUNT(*) as count')
            ->groupBy('log_name')
            ->pluck('count', 'log_name')
            ->toArray();

        return [
            'recent_logs' => $recentLogs,
            'today_count' => $todayCount,
            'by_type' => $byLogName,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        return DB::table('activity_log')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->orderBy('created_at', 'desc')
            ->get(['log_name', 'description', 'subject_type', 'created_at'])
            ->map(fn ($log) => [
                'type' => $log->log_name,
                'description' => $log->description,
                'subject' => class_basename($log->subject_type ?? ''),
                'created_at' => $log->created_at,
            ])->toArray();
    }
}
