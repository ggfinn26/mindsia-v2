<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\EmployeePayroll;
use App\Models\MemberPayment;
use App\Services\Dashboard\WidgetDataProviderInterface;
use Carbon\Carbon;

class AccountReconciliationWidget implements WidgetDataProviderInterface
{
    public function getKey(): string
    {
        return 'account_reconciliation';
    }

    public function getData(?int $branchId = null): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // AR: member payments pending (receivable)
        $arTotal = MemberPayment::where('payment_status', 'pending')
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        $arCount = MemberPayment::where('payment_status', 'pending')
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->count();

        // AP: payroll pending (payable)
        $apTotal = EmployeePayroll::where('payment_status', 'pending')
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->sum('net_amount');

        $apCount = EmployeePayroll::where('payment_status', 'pending')
            ->when($branchId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('branch_id', $branchId)))
            ->count();

        // Collected this month
        $collectedThisMonth = MemberPayment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->sum('amount');

        return [
            'ar_total' => $arTotal,
            'ar_count' => $arCount,
            'ap_total' => $apTotal,
            'ap_count' => $apCount,
            'collected_this_month' => $collectedThisMonth,
            'net_position' => $collectedThisMonth - $apTotal,
            'period' => $now->translatedFormat('F Y'),
        ];
    }

    public function getExportData(?int $branchId = null): array
    {
        $payments = MemberPayment::where('payment_status', 'pending')
            ->when($branchId, fn ($q) => $q->whereHas('registration', fn ($r) => $r->where('branch_id', $branchId)))
            ->with('registration.member:id,full_name')
            ->orderBy('due_date')
            ->get();

        return $payments->map(fn ($p) => [
            'member' => $p->registration?->member?->full_name ?? '-',
            'amount' => $p->amount,
            'due_date' => $p->due_date?->format('Y-m-d'),
            'installment' => $p->installment_number,
        ])->toArray();
    }
}
