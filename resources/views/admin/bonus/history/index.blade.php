@extends('layouts.dashboard')

@section('title', 'Riwayat Perubahan Bonus - MINDSIA')
@section('header_title', 'Riwayat Perubahan Bonus')

@section('content')
@php
    $typeLabels = ['marketing' => 'Marketing', 'kpi' => 'KPI', 'special' => 'Spesial'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Riwayat Perubahan Bonus</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Audit log seluruh perubahan konfigurasi bonus rule yang tercatat otomatis oleh sistem.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('bonus.marketing-rules.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                Aturan Bonus Marketing
            </a>
            <a href="{{ route('bonus.kpi-rules.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                Aturan Bonus KPI
            </a>
            <a href="{{ route('bonus.special-rules.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">stars</span>
                Aturan Bonus Spesial
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="rounded-2xl shadow-sm p-5"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('bonus.history.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-64">
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                    Filter Tipe Bonus
                </label>
                <select name="bonus_type"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Tipe Bonus</option>
                    @foreach ($typeLabels as $val => $label)
                        <option value="{{ $val }}" @selected(request('bonus_type') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filter
                </button>
                @if (request()->filled('bonus_type') || request()->filled('rule_id'))
                    <a href="{{ route('bonus.history.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[18px]">refresh</span>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Data Table Riwayat Perubahan --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">history</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Daftar Log Riwayat Perubahan</h2>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md"
                  style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                {{ $histories->total() }} Catatan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Waktu</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Tipe</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Rule ID</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Aksi</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Diubah Oleh</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse ($histories as $h)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            class="align-top"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-6 py-4 whitespace-nowrap text-xs" style="color: var(--dash-text-muted);">
                                {{ $h->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold"
                                      style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                                    {{ $typeLabels[$h->bonus_type] ?? $h->bonus_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs" style="color: var(--dash-text-muted);">
                                #{{ $h->rule_id }}
                            </td>
                            <td class="px-6 py-4 font-medium" style="color: var(--dash-text-heading);">
                                {{ $h->action }}
                            </td>
                            <td class="px-6 py-4" style="color: var(--dash-text-body);">
                                {{ $h->changedBy?->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($h->new_values || $h->old_values)
                                    <details class="text-xs">
                                        <summary class="cursor-pointer font-semibold transition-colors"
                                                 style="color: var(--dash-primary-text);">
                                            Lihat diff
                                        </summary>
                                        <div class="mt-2 space-y-1.5 p-3 rounded-lg font-mono text-[11px]"
                                             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                            @if ($h->old_values)
                                                <p class="break-all" style="color: var(--dash-danger);">
                                                    - {{ json_encode($h->old_values, JSON_UNESCAPED_UNICODE) }}
                                                </p>
                                            @endif
                                            @if ($h->new_values)
                                                <p class="break-all" style="color: var(--dash-status-success-text, #059669);">
                                                    + {{ json_encode($h->new_values, JSON_UNESCAPED_UNICODE) }}
                                                </p>
                                            @endif
                                        </div>
                                    </details>
                                @else
                                    <span style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[44px] block mx-auto mb-2" style="color: var(--dash-text-faint);">history</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada riwayat perubahan.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Semua aktivitas perubahan bonus rule akan tercatat otomatis di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($histories->hasPages())
            <div class="px-6 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $histories->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
