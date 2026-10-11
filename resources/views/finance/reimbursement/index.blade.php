@extends('layouts.dashboard')

@section('title', 'Reimbursement Saya')
@section('header_title', 'Reimbursement')

@section('content')
@php
    $statusConfig = [
        'DRAFT'    => ['bg' => '#f8fafc', 'text' => '#475569', 'label' => 'Draft'],
        'PENDING'  => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'Menunggu'],
        'APPROVED' => ['bg' => '#f0fdf4', 'text' => '#166534', 'label' => 'Disetujui'],
        'REJECTED' => ['bg' => '#fef2f2', 'text' => '#991b1b', 'label' => 'Ditolak'],
        'PAID'     => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'label' => 'Dibayar'],
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Finance</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Reimbursement Saya</h1>
        </div>
        @can('finance.reimbursement.create')
            <a href="{{ route('reimbursements.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Reimbursement
            </a>
        @endcan
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Tujuan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Periode Pengeluaran</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Total</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reimbursements as $r)
                    @php
                        $sc = $statusConfig[$r->status] ?? ['bg' => '#f8fafc', 'text' => '#475569', 'label' => $r->status];
                    @endphp
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 font-semibold" style="color: var(--dash-text-heading);">{{ $r->business_purpose }}</td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                            {{ $r->expense_period_start?->format('d M Y') }} – {{ $r->expense_period_end?->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums font-bold" style="color: #059669;">
                            Rp {{ number_format($r->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                  style="background-color: {{ $sc['bg'] }}; color: {{ $sc['text'] }};">
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('reimbursements.show', $r) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">receipt</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada reimbursement.</p>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Klik "Buat Reimbursement" untuk mulai mengajukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($reimbursements, 'hasPages') && $reimbursements->hasPages())
        <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
            {{ $reimbursements->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
