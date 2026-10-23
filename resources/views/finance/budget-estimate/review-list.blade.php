@extends('layouts.dashboard')

@section('title', 'Antrean Review Anggaran')
@section('header_title', 'Antrean Review Anggaran')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $statusLabel = [
        'ops_review'     => 'Menunggu Review OPS',
        'finance_review' => 'Menunggu Review Finance',
        'accepted'       => 'Diterima - Belum Dikirim',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('budget-estimates.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Anggaran Cabang
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Keuangan</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Antrean Review Anggaran</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                {{ $statusLabel[$status] ?? $status }} - Daftar estimasi anggaran cabang yang memerlukan peninjauan lanjutan.
            </p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
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
                            style="color: var(--dash-text-muted);">Judul & Cabang</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Periode & Pembuat</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Total Anggaran</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse ($estimates as $est)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3">
                                <a href="{{ route('budget-estimates.show', $est) }}"
                                   class="font-semibold block hover:underline transition-colors text-xs"
                                   style="color: var(--dash-primary-text);">
                                    {{ $est->title }}
                                </a>
                                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ $est->branch?->branch_name ?? '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs font-medium" style="color: var(--dash-text-heading);">
                                    {{ $months[$est->period_month] ?? '-' }} {{ $est->period_year }}
                                </div>
                                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">
                                    Diajukan: {{ $est->submittedBy?->full_name ?? '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-xs font-bold"
                                style="color: var(--dash-text-heading);">
                                Rp {{ number_format($est->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('budget-estimates.show', $est) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90"
                                   style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">rate_review</span>
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">task_alt</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Tidak ada anggaran yang perlu di-review.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Semua pengajuan anggaran pada tahap ini sudah diproses.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($estimates->hasPages())
            <div class="px-4 py-3 border-t" style="border-color: var(--dash-card-border);">
                {{ $estimates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
