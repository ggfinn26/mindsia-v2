@extends('layouts.dashboard')

@section('title', 'Review Reimbursement')
@section('header_title', 'Review Reimbursement')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Finance</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Review Reimbursement</h1>
        <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Reimbursement yang menunggu persetujuan finance.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 px-4 py-3 text-sm text-emerald-800 [.mode-dark_&]:text-emerald-400">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden border border-[var(--dash-input-border)] bg-[var(--dash-card-bg)]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-semibold text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-5 py-3">Pegawai</th>
                        <th class="px-5 py-3">Tujuan</th>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse ($reimbursements as $r)
                        <tr class="hover:bg-[var(--dash-page-bg)]/70">
                            <td class="px-5 py-4 font-semibold text-[var(--dash-text-heading)]">{{ $r->employee?->full_name }}</td>
                            <td class="px-5 py-4 text-[var(--dash-text-body)]">{{ $r->business_purpose }}</td>
                            <td class="px-5 py-4 text-[var(--dash-text-muted)]">{{ $r->expense_period_start?->format('d M') }} – {{ $r->expense_period_end?->format('d M Y') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold text-[var(--dash-text-heading)]">Rp {{ number_format($r->total_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('reimbursements.show', $r) }}" class="inline-flex min-h-9 items-center bg-[var(--dash-primary)] px-3 text-xs font-semibold text-white hover:opacity-90">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-[var(--dash-text-faint)]">check_circle</span>
                                <p class="mt-2 font-semibold text-[var(--dash-text-body)]">Tidak ada reimbursement yang perlu direviu.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($reimbursements, 'hasPages') && $reimbursements->hasPages())
            <div class="border-t border-[var(--dash-input-border)] px-5 py-4">{{ $reimbursements->links() }}</div>
        @endif
    </div>
</div>
@endsection
