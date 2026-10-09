@extends('layouts.dashboard')

@section('title', 'Reimbursement Saya')
@section('header_title', 'Reimbursement')

@section('content')
@php
    $statusBadge = [
        'DRAFT'    => 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)] ring-1 ring-inset ring-slate-500/10 [.mode-dark_&]:ring-slate-500/30',
        'PENDING'  => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 [.mode-dark_&]:bg-amber-500/10 [.mode-dark_&]:text-amber-400 [.mode-dark_&]:ring-amber-500/20',
        'APPROVED' => 'bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 text-emerald-700 [.mode-dark_&]:text-emerald-400 ring-1 ring-inset ring-emerald-600/20 [.mode-dark_&]:bg-emerald-50 [.mode-dark_&]:bg-emerald-500/100/10 [.mode-dark_&]:text-emerald-400 [.mode-dark_&]:ring-emerald-500/20',
        'REJECTED' => 'bg-red-50 [.mode-dark_&]:bg-red-500/10 text-red-700 [.mode-dark_&]:text-red-400 ring-1 ring-inset ring-red-600/10 [.mode-dark_&]:bg-red-50 [.mode-dark_&]:bg-red-500/100/10 [.mode-dark_&]:text-red-400 [.mode-dark_&]:ring-red-500/20',
        'PAID'     => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10 [.mode-dark_&]:bg-blue-500/10 [.mode-dark_&]:text-blue-400 [.mode-dark_&]:ring-blue-500/20',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[var(--dash-text-muted)]">Finance</p>
            <h2 class="font-jakarta text-[var(--dash-text-muted)]xl font-bold tracking-tight text-[var(--dash-text-heading)]">Reimbursement Saya</h2>
        </div>
        @can('finance.reimbursement.create')
            <a href="{{ route('reimbursements.create') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-[var(--dash-primary)] px-5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span> Buat Reimbursement
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-100 [.mode-dark_&]:border-emerald-500/20 bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 p-4 text-sm text-emerald-800 [.mode-dark_&]:text-emerald-400 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-6 py-4">Tujuan</th>
                        <th class="px-6 py-4">Periode Pengeluaran</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse ($reimbursements as $r)
                        <tr class="hover:bg-[var(--dash-page-bg)] transition-colors">
                            <td class="px-6 py-4 font-semibold text-[var(--dash-text-heading)]">{{ $r->business_purpose }}</td>
                            <td class="px-6 py-4 font-medium text-[var(--dash-text-muted)]">
                                {{ $r->expense_period_start?->format('d M Y') }} – {{ $r->expense_period_end?->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums font-bold text-emerald-600">Rp {{ number_format($r->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider {{ $statusBadge[$r->status] ?? 'bg-[var(--dash-input-bg)] text-[var(--dash-text-muted)]' }}">{{ $r->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('reimbursements.show', $r) }}" class="inline-flex min-h-9 items-center rounded-lg border border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-3 text-xs font-bold text-[var(--dash-text-body)] shadow-sm hover:bg-[var(--dash-page-bg)] hover:text-[var(--dash-text-heading)] transition-colors">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-[var(--dash-text-muted)]enter">
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[var(--dash-page-bg)] mb-3">
                                    <span class="material-symbols-outlined text-[var(--dash-text-faint)] text-[var(--dash-text-muted)]xl">receipt</span>
                                </div>
                                <h3 class="text-sm font-medium text-[var(--dash-text-heading)]">Belum ada reimbursement</h3>
                                <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Klik "Buat Reimbursement" untuk mulai mengajukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($reimbursements, 'hasPages') && $reimbursements->hasPages())
            <div class="border-t border-[var(--dash-card-border)] bg-[var(--dash-page-bg)] px-6 py-4">{{ $reimbursements->links() }}</div>
        @endif
    </div>
</div>
@endsection
