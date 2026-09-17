<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\MemberPayment;
use App\Models\Reimbursement;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class InvoiceBillManagementWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'invoice_bill_management';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Incoming: member payments
        $incomingPaid = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $incomingPending = MemberPayment::where('payment_status', 'pending')
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        // Outgoing: reimbursements (bills to pay)
        $outgoingPaid = Reimbursement::where('status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        $outgoingPending = Reimbursement::whereIn('status', ['approved', 'pending'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        return [
            'incoming_paid' => $incomingPaid,
            'incoming_pending' => $incomingPending,
            'outgoing_paid' => $outgoingPaid,
            'outgoing_pending' => $outgoingPending,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $now = Carbon::now();

        $reimbursements = Reimbursement::whereYear(
            fn ($q) => $q->whereYear('paid_at', $now->year)->orWhereYear('created_at', $now->year),
            $now->year
        )
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('employee:id,full_name', 'branch:id,branch_name')
            ->orderBy('created_at', 'desc')
            ->get();

        return $reimbursements->map(fn ($r) => [
            'employee' => $r->employee?->full_name ?? '-',
            'branch' => $r->branch?->branch_name ?? '-',
            'total_amount' => $r->total_amount,
            'status' => $r->status,
            'submitted_at' => $r->created_at?->format('Y-m-d'),
        ])->toArray();
    }
}
