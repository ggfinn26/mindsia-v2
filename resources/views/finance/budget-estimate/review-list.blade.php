@extends('layouts.dashboard')

@section('title', 'Antrean Review Anggaran')
@section('header_title', 'Antrean Review Anggaran')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $statusLabel = [
        'ops_review'      => 'Menunggu OPS',
        'finance_review'  => 'Menunggu Finance',
        'accepted'        => 'Diterima — Belum Dikirim',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('budget-estimates.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Anggaran Cabang
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Antrean Review</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $statusLabel[$status] ?? $status }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Judul</th>
                        <th class="px-5 py-3">Cabang</th>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3">Diajukan oleh</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($estimates as $est)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $est->title }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $est->branch?->branch_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $months[$est->period_month] ?? '-' }} {{ $est->period_year }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $est->submittedBy?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-900">Rp {{ number_format($est->total_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('budget-estimates.show', $est) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">task_alt</span>
                                <p class="mt-2 font-semibold text-slate-700">Tidak ada anggaran yang perlu di-review.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($estimates->hasPages())
            <div class="border-t border-slate-200 px-5 py-3">{{ $estimates->links() }}</div>
        @endif
    </div>
</div>
@endsection
