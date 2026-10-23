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
        'inventory_draft'=> 'bg-teal-100 text-teal-800',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Keuangan</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Anggaran Cabang</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Estimasi anggaran bulanan operasional cabang dan alur pengajuan.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @canany(['finance.budget_estimate.ops_review', 'finance.budget_estimate.send'])
                <a href="{{ route('budget-estimates.review-list') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                    Antrean Review
                </a>
            @endcanany
            @can('finance.budget_estimate.create')
                <a href="{{ route('budget-estimates.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Buat Anggaran
                </a>
            @endcan
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

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('budget-estimates.index') }}"
          class="p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            {{-- Bulan --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Bulan Periode</label>
                <select name="month"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach (array_slice($months, 1, null, true) as $num => $label)
                        <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Tahun Periode</label>
                <input type="number" name="year" value="{{ $year }}" min="2020"
                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
            </div>

            {{-- Filter & Reset Actions --}}
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filter
                </button>
                <a href="{{ route('budget-estimates.index') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    Reset
                </a>
            </div>
        </div>
    </form>

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
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">Status</th>
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
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $statusBadge[$est->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ str_replace('_', ' ', ucfirst($est->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('budget-estimates.show', $est) }}"
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
                            <td colspan="5" class="px-4 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">receipt_long</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada anggaran bulan ini.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Gunakan tombol Buat Anggaran di atas untuk membuat pengajuan baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
