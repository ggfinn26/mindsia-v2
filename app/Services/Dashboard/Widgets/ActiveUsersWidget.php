<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\User;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActiveUsersWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'active_users';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        // Sessions table for active sessions
        $activeSessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $now->copy()->subMinutes(15)->timestamp)
            ->count();

        $totalUsers = User::where('is_active', true)->count();

        $recentLogins = User::where('last_login_at', '>=', $now->copy()->subDay())
            ->with('employee:id,user_id,full_name')
            ->orderBy('last_login_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($u) => [
                'name' => $u->employee?->full_name ?? $u->email,
                'last_login' => $u->last_login_at?->diffForHumans(),
            ])->toArray();

        $newUsersThisMonth = User::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        return [
            'active_now' => $activeSessions,
            'total_active_users' => $totalUsers,
            'recent_logins' => $recentLogins,
            'new_this_month' => $newUsersThisMonth,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        return User::where('is_active', true)
            ->with('employee:id,user_id,full_name')
            ->orderBy('last_login_at', 'desc')
            ->get()
            ->map(fn ($u) => [
                'email' => $u->email,
                'name' => $u->employee?->full_name ?? '-',
                'last_login' => $u->last_login_at?->format('Y-m-d H:i'),
                'is_active' => $u->is_active ? 'Ya' : 'Tidak',
            ])->toArray();
    }
}
