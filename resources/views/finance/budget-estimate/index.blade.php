@extends('layouts.dashboard')

@section('title', 'Anggaran Cabang')
@section('header_title', 'Anggaran Cabang')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $statusBadge = [
        'draft'          => 'bg-slate-100 text-slate-700',
        'ops_review'     => 'bg-blue-100 text-blue-800',
        'finance_review' => 'bg-amber-100 text-amber-800',
        'accepted'       => 'bg-emerald-100 text-emerald-800',
        'sent'           => 'bg-purple-100 text-purple-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Finance</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Anggaran Cabang</h2>
        </div>
        @can('finance.budget_estimate.create')
            <a href="{{ route('budget-estimates.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">add</span> Buat Anggaran
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <form method="GET" class="flex flex-wrap gap-3 border border-slate-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Bulan</label>
            <select name="month" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" class="block w-24 border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
        </div>
        <div class="flex items-end">
            <button type="submit" class="inline-flex min-h-9 items-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Filter</button>
        </div>
    </form>

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Judul</th>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3">Diajukan oleh</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($estimates as $est)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $est->title }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $months[$est->period_month] ?? '-' }} {{ $est->period_year }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $est->submittedBy?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-900">Rp {{ number_format($est->total_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$est->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ str_replace('_', ' ', ucfirst($est->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('budget-estimates.show', $est) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">receipt_long</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada anggaran bulan ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
