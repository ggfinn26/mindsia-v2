@extends('layouts.dashboard')

@section('title', 'Detail Snapshot MPI')
@section('header_title', 'Detail MPI')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $latest = $history->last();
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex items-start gap-3">
        <a href="{{ route('marketing-kpi.index', ['month' => $month, 'year' => $year]) }}"
           class="mt-1 hover:opacity-70 transition-opacity shrink-0"
           style="color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Sales & Service</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $employee->full_name }}</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">{{ $latest?->branch_name_snapshot ?? $employee->branch?->branch_name ?? '—' }} · {{ $months[$month] }} {{ $year }}</p>
        </div>
    </div>

    @if(!$latest)
    <div class="rounded-2xl px-5 py-16 text-center"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <span class="material-symbols-outlined text-5xl mb-3 block" style="color: var(--dash-text-faint);">leaderboard</span>
        <p class="font-semibold" style="color: var(--dash-text-heading);">Belum ada snapshot untuk periode ini.</p>
    </div>
    @else

    {{-- Ranking --}}
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wider mb-3" style="color: var(--dash-text-faint);">Ranking {{ $months[$month] }} {{ $year }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
                ['icon' => 'location_on',  'color' => 'var(--dash-primary)', 'bg' => 'rgba(85,134,219,0.1)',  'label' => 'Rank Area',     'value' => $latest->rank_area    ? '#'.$latest->rank_area    : '—', 'sub' => $latest->area_name_snapshot   ?: '—'],
                ['icon' => 'map',          'color' => '#7c3aed',             'bg' => 'rgba(124,58,237,0.1)', 'label' => 'Rank Region',   'value' => $latest->rank_region  ? '#'.$latest->rank_region  : '—', 'sub' => $latest->region_name_snapshot ?: '—'],
                ['icon' => 'public',       'color' => '#d97706',             'bg' => 'rgba(217,119,6,0.1)',  'label' => 'Rank Nasional', 'value' => $latest->rank_national ? '#'.$latest->rank_national : '—', 'sub' => ''],
            ] as $card)
            <div class="rounded-2xl p-5 flex items-center gap-4"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center"
                     style="background-color: {{ $card['bg'] }};">
                    <span class="material-symbols-outlined text-[22px]" style="color: {{ $card['color'] }};">{{ $card['icon'] }}</span>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);">{{ $card['label'] }}</p>
                    <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $card['value'] }}</p>
                    @if($card['sub'])
                        <p class="text-xs" style="color: var(--dash-text-faint);">{{ $card['sub'] }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Metrik --}}
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wider mb-3" style="color: var(--dash-text-faint);">Skor & Metrik</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <div class="rounded-2xl p-5 flex items-center gap-4"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-primary); border-opacity: 0.3;">
                <div class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center"
                     style="background-color: color-mix(in srgb, var(--dash-primary) 12%, transparent);">
                    <span class="material-symbols-outlined text-[22px]" style="color: var(--dash-primary);">format_list_numbered</span>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Skor MPI</p>
                    <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-primary);">{{ number_format((float)$latest->mpi_score, 2) }}</p>
                    <p class="text-xs" style="color: var(--dash-text-faint);">50% kelas + 50% omzet</p>
                </div>
            </div>

            <div class="rounded-2xl p-5"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Kelas</p>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                        {{ (float)$latest->classes_percentage >= 100 ? 'bg-emerald-100 text-emerald-700' : ((float)$latest->classes_percentage >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                        {{ number_format((float)$latest->classes_percentage, 1) }}%
                    </span>
                </div>
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $latest->classes_actual }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Target: {{ $latest->classes_target }} kelas</p>
                <div class="mt-3 h-1.5 rounded-full overflow-hidden" style="background-color: var(--dash-card-border);">
                    <div class="h-full rounded-full" style="width: {{ min(100, (float)$latest->classes_percentage) }}%; background-color: var(--dash-primary);"></div>
                </div>
            </div>

            <div class="rounded-2xl p-5"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Omzet</p>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                        {{ (float)$latest->registration_value_percentage >= 100 ? 'bg-emerald-100 text-emerald-700' : ((float)$latest->registration_value_percentage >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                        {{ number_format((float)$latest->registration_value_percentage, 1) }}%
                    </span>
                </div>
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">Rp {{ number_format((float)$latest->registration_value_actual, 0, ',', '.') }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Target: Rp {{ number_format((float)$latest->omzet_target, 0, ',', '.') }}</p>
                <div class="mt-3 h-1.5 rounded-full overflow-hidden" style="background-color: var(--dash-card-border);">
                    <div class="h-full rounded-full bg-violet-500" style="width: {{ min(100, (float)$latest->registration_value_percentage) }}%"></div>
                </div>
            </div>

        </div>
    </div>

    {{-- Riwayat snapshot --}}
    @if($history->count() > 1)
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Riwayat Snapshot Bulan Ini</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">MPI</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Kelas</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Omzet</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-center" style="color: var(--dash-text-muted);">Rank Nasional</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history->reverse() as $snap)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 font-bold" style="color: var(--dash-primary);">{{ number_format((float)$snap->mpi_score, 2) }}</td>
                        <td class="px-5 py-3.5 text-right tabular-nums" style="color: var(--dash-text-body);">
                            {{ $snap->classes_actual }}/{{ $snap->classes_target }}
                            <span class="text-xs" style="color: var(--dash-text-faint);">({{ number_format((float)$snap->classes_percentage, 1) }}%)</span>
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums" style="color: var(--dash-text-body);">
                            Rp {{ number_format((float)$snap->registration_value_actual, 0, ',', '.') }}
                            <span class="text-xs" style="color: var(--dash-text-faint);">({{ number_format((float)$snap->registration_value_percentage, 1) }}%)</span>
                        </td>
                        <td class="px-5 py-3.5 text-center font-medium" style="color: var(--dash-text-body);">#{{ $snap->rank_national }}</td>
                        <td class="px-5 py-3.5">
                            @if($snap->isFinalized())
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">Final</span>
                            @elseif($loop->first)
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Terbaru</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Lama</span>
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
