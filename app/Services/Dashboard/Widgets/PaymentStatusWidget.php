<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class PaymentStatusWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'payment_status';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $byStatus = MemberPayment::when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->selectRaw('payment_status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_status')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->payment_status => [
                'count' => $row->count,
                'total' => $row->total,
            ]])
            ->toArray();

        $dueSoon = MemberPayment::where('payment_status', 'pending')
            ->where('due_date', '<=', $now->copy()->addDays(7))
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->count();

        $overdue = MemberPayment::where('payment_status', 'pending')
            ->where('due_date', '<', $now)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->count();

        return [
            'by_status' => $byStatus,
            'due_soon_count' => $dueSoon,
            'overdue_count' => $overdue,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $payments = MemberPayment::when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->with('registration.member:id,full_name')
            ->orderBy('due_date')
            ->get();

        return $payments->map(fn ($p) => [
            'member' => $p->registration?->member?->full_name ?? '-',
            'amount' => $p->amount,
            'payment_status' => $p->payment_status,
            'due_date' => $p->due_date?->format('Y-m-d'),
            'paid_at' => $p->paid_at?->format('Y-m-d'),
        ])->toArray();
    }
}
