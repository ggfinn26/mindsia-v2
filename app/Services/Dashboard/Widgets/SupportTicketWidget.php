<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberSupportTicket;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class SupportTicketWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'support_ticket';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $user = auth()->user();
        $employeeId = $user?->employee?->id;

        if (! $employeeId) {
            return ['error' => 'Employee not found'];
        }

        // Support tickets assigned to this employee
        $tickets = MemberSupportTicket::where('assigned_employee_id', $employeeId)
            ->get();

        $byStatus = $tickets->groupBy('status')
            ->map(fn ($g) => $g->count())
            ->toArray();

        $openCount = ($byStatus['open'] ?? 0) + ($byStatus['in_progress'] ?? 0);
        $resolvedThisMonth = $tickets->where('status', 'resolved')
            ->filter(fn ($t) => $t->updated_at?->month === $now->month && $t->updated_at?->year === $now->year)
            ->count();

        $recentTickets = $tickets->sortByDesc('created_at')->take(5)->map(fn ($t) => [
            'subject' => $t->subject ?? '-',
            'status' => $t->status,
            'created_at' => $t->created_at?->format('Y-m-d'),
        ])->values()->toArray();

        return [
            'by_status' => $byStatus,
            'open_count' => $openCount,
            'resolved_this_month' => $resolvedThisMonth,
            'recent_tickets' => $recentTickets,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $employeeId = auth()->user()?->employee?->id;

        if (! $employeeId) {
            return [];
        }

        return MemberSupportTicket::where('assigned_employee_id', $employeeId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($t) => [
                'subject' => $t->subject ?? '-',
                'status' => $t->status,
                'priority' => $t->priority ?? '-',
                'created_at' => $t->created_at?->format('Y-m-d'),
            ])->toArray();
    }
}
