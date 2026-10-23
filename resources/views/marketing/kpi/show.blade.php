@extends('layouts.dashboard')

@section('title', 'Detail Snapshot MPI')
@section('header_title', 'Detail MPI')

@section('content')
@php
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $latest = $history->last();
@endphp

<div class="space-y-6">

    {{-- Pola B3 Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('marketing-kpi.index', ['month' => $month, 'year' => $year]) }}" 
                   class="dash-back inline-flex items-center gap-1 text-xs font-semibold transition-colors"
                   style="color: var(--dash-text-muted)">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Kembali ke KPI Ranking
                </a>
            </div>
            <div class="text-xs font-semibold tracking-wider uppercase mb-1" style="color: var(--dash-primary-text)">Detail Kinerja Marketing</div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading)">{{ $employee->full_name }}</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted)">
                {{ $latest?->branch_name_snapshot ?? $employee->branch?->branch_name ?? '-' }} · {{ $months[$month] }} {{ $year }}
            </p>
        </div>
    </div>

    @if(!$latest)
        <div class="rounded-2xl border px-5 py-16 text-center shadow-sm"
            style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
            <span class="material-symbols-outlined text-5xl opacity-40 mb-3 block" style="color: var(--dash-text-faint)">leaderboard</span>
            <p class="font-semibold" style="color: var(--dash-text-heading)">Belum ada snapshot untuk periode ini.</p>
            <p class="text-xs sm:text-sm mt-1" style="color: var(--dash-text-muted)">Pilih periode lain yang memiliki data kalkulasi MPI.</p>
        </div>
    @else

        {{-- Ranking Section --}}
        <div class="space-y-3">
            <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted)">
                Ranking {{ $months[$month] }} {{ $year }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Rank Area --}}
                <div class="rounded-2xl border p-5 shadow-sm flex items-center gap-4"
                    style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center"
                        style="background-color: color-mix(in srgb, var(--dash-primary) 12%, transparent); color: var(--dash-primary)">
                        <span class="material-symbols-outlined text-[22px]">location_on</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted)">Rank Area</p>
                        <p class="text-2xl font-bold" style="color: var(--dash-text-heading)">{{ $latest->rank_area ? '#'.$latest->rank_area : '-' }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted)">{{ $latest->area_name_snapshot ?: '-' }}</p>
                    </div>
                </div>

                {{-- Rank Region --}}
                <div class="rounded-2xl border p-5 shadow-sm flex items-center gap-4"
                    style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-violet-500/10 flex items-center justify-center text-violet-600 dark:text-violet-400">
                        <span class="material-symbols-outlined text-[22px]">map</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted)">Rank Region</p>
                        <p class="text-2xl font-bold" style="color: var(--dash-text-heading)">{{ $latest->rank_region ? '#'.$latest->rank_region : '-' }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted)">{{ $latest->region_name_snapshot ?: '-' }}</p>
                    </div>
                </div>

                {{-- Rank Nasional --}}
                <div class="rounded-2xl border p-5 shadow-sm flex items-center gap-4"
                    style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <span class="material-symbols-outlined text-[22px]">public</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted)">Rank Nasional</p>
                        <p class="text-2xl font-bold" style="color: var(--dash-text-heading)">{{ $latest->rank_national ? '#'.$latest->rank_national : '-' }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted)">Skala Seluruh Cabang</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metrik Section --}}
        <div class="space-y-3">
            <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted)">Skor & Metrik</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <div class="rounded-2xl border p-5 shadow-sm flex items-center gap-4"
                    style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center"
                        style="background-color: color-mix(in srgb, var(--dash-primary) 12%, transparent); color: var(--dash-primary)">
                        <span class="material-symbols-outlined text-[22px]">format_list_numbered</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted)">Skor MPI</p>
                        <p class="text-2xl font-bold tabular-nums" style="color: var(--dash-primary)">{{ number_format((float)$latest->mpi_score, 2) }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted)">50% kelas + 50% omzet</p>
                    </div>
                </div>

                <div class="rounded-2xl border p-5 shadow-sm"
                    style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted)">Realisasi Kelas</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ (float)$latest->classes_percentage >= 100 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : ((float)$latest->classes_percentage >= 50 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20') }}">
                            {{ number_format((float)$latest->classes_percentage, 1) }}%
                        </span>
                    </div>
                    <p class="text-xl font-bold" style="color: var(--dash-text-heading)">{{ $latest->classes_actual }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted)">Target: {{ $latest->classes_target }} kelas</p>
                    <div class="mt-3 h-1.5 rounded-full overflow-hidden" style="background-color: var(--dash-nav-hover-bg)">
                        <div class="h-full rounded-full transition-all" style="width: {{ min(100, (float)$latest->classes_percentage) }}%; background-color: var(--dash-primary)"></div>
                    </div>
                </div>

                <div class="rounded-2xl border p-5 shadow-sm"
                    style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted)">Realisasi Omzet</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ (float)$latest->registration_value_percentage >= 100 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : ((float)$latest->registration_value_percentage >= 50 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20') }}">
                            {{ number_format((float)$latest->registration_value_percentage, 1) }}%
                        </span>
                    </div>
                    <p class="text-xl font-bold" style="color: var(--dash-text-heading)">Rp {{ number_format((float)$latest->registration_value_actual, 0, ',', '.') }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted)">Target: Rp {{ number_format((float)$latest->omzet_target, 0, ',', '.') }}</p>
                    <div class="mt-3 h-1.5 rounded-full overflow-hidden" style="background-color: var(--dash-nav-hover-bg)">
                        <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ min(100, (float)$latest->registration_value_percentage) }}%"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Riwayat snapshot Table Card with Zero Horizontal Scroll --}}
        @if($history->count() > 1)
        <div class="rounded-2xl border shadow-sm overflow-hidden"
            style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
            <div class="border-b px-5 py-4"
                style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg)">
                <h2 class="text-sm font-bold" style="color: var(--dash-text-heading)">Riwayat Snapshot Bulan Ini</h2>
            </div>
            <div class="w-full">
                <table class="w-full text-sm text-left">
                    <thead class="border-b text-[11px] font-semibold uppercase tracking-wider"
                        style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted)">
                        <tr>
                            <th class="px-5 py-3.5">Skor MPI</th>
                            <th class="px-5 py-3.5 text-right">Target & Kelas Aktual</th>
                            <th class="px-5 py-3.5 text-right">Target & Omzet Aktual</th>
                            <th class="px-5 py-3.5 text-center">Rank Nasional</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border)">
                        @foreach($history->reverse() as $snap)
                        <tr onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'" onmouseout="this.style.backgroundColor='transparent'" 
                            class="transition-colors">
                            <td class="px-5 py-3.5 font-bold tabular-nums text-base" style="color: var(--dash-primary)">
                                {{ number_format((float)$snap->mpi_score, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums">
                                <div class="font-medium" style="color: var(--dash-text-heading)">
                                    {{ $snap->classes_actual }}/{{ $snap->classes_target }} kelas
                                </div>
                                <div class="text-xs mt-0.5" style="color: var(--dash-text-muted)">
                                    {{ number_format((float)$snap->classes_percentage, 1) }}%
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums">
                                <div class="font-medium" style="color: var(--dash-text-heading)">
                                    Rp {{ number_format((float)$snap->registration_value_actual, 0, ',', '.') }}
                                </div>
                                <div class="text-xs mt-0.5" style="color: var(--dash-text-muted)">
                                    {{ number_format((float)$snap->registration_value_percentage, 1) }}%
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center font-semibold" style="color: var(--dash-text-heading)">
                                #{{ $snap->rank_national }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($snap->isFinalized())
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        Final
                                    </span>
                                @elseif($loop->first)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold"
                                        style="background-color: color-mix(in srgb, var(--dash-primary) 12%, transparent); color: var(--dash-primary)">
                                        Terbaru
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold border"
                                        style="border-color: var(--dash-card-border); color: var(--dash-text-muted); background-color: var(--dash-nav-hover-bg)">
                                        Lama
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    @endif
</div>
@endsection
