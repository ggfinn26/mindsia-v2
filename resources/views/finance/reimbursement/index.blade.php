@extends('layouts.dashboard')

@section('title', 'Reimbursement Saya')
@section('header_title', 'Reimbursement')

@section('content')
@php
    $statusConfig = [
        'DRAFT'    => ['bg' => 'bg-slate-100 text-slate-700', 'label' => 'Draft'],
        'PENDING'  => ['bg' => 'bg-amber-100 text-amber-800', 'label' => 'Menunggu Review'],
        'APPROVED' => ['bg' => 'bg-emerald-100 text-emerald-800', 'label' => 'Disetujui'],
        'REJECTED' => ['bg' => 'bg-red-100 text-red-800', 'label' => 'Ditolak'],
        'PAID'     => ['bg' => 'bg-blue-100 text-blue-800', 'label' => 'Dibayar'],
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Keuangan</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Reimbursement Saya</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola dan pantau pengajuan klaim penggantian pengeluaran operasional Anda.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('finance.reimbursement.review')
                <a href="{{ route('reimbursements.review-list') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                    Antrean Review
                </a>
            @endcan
            @can('finance.reimbursement.create')
                <a href="{{ route('reimbursements.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Buat Reimbursement
                </a>
            @endcan
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tujuan & Periode Pengeluaran</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Total Pengajuan</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($reimbursements as $r)
                        @php
                            $sc = $statusConfig[$r->status] ?? ['bg' => 'bg-slate-100 text-slate-700', 'label' => $r->status];
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3">
                                <a href="{{ route('reimbursements.show', $r) }}"
                                   class="font-semibold block hover:underline transition-colors text-xs"
                                   style="color: var(--dash-primary-text);">
                                    {{ $r->business_purpose }}
                                </a>
                                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ $r->expense_period_start?->format('d M Y') }} - {{ $r->expense_period_end?->format('d M Y') }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-xs font-bold"
                                style="color: var(--dash-primary-text);">
                                Rp {{ number_format($r->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $sc['bg'] }}">
                                    {{ $sc['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('reimbursements.show', $r) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">receipt</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada reimbursement.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Klik tombol "Buat Reimbursement" untuk mulai mengajukan klaim baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($reimbursements, 'hasPages') && $reimbursements->hasPages())
            <div class="px-4 py-3 border-t" style="border-color: var(--dash-card-border);">
                {{ $reimbursements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
