@extends('layouts.dashboard')

@section('title', 'Detail Snapshot MPI')
@section('header_title', 'Detail MPI')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $latest = $history->last();
@endphp

<div class="w-full space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('marketing-kpi.index', ['month' => $month, 'year' => $year]) }}"
           class="text-slate-400 hover:text-slate-700 transition-colors">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h2 class="font-jakarta text-xl font-bold tracking-tight text-slate-900">{{ $employee->full_name }}</h2>
            <p class="text-sm text-slate-500">{{ $latest?->branch_name_snapshot ?? $employee->branch?->branch_name ?? '—' }} · {{ $months[$month] }} {{ $year }}</p>
        </div>
    </div>

    @if(!$latest)
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-5 py-16 text-center">
        <span class="material-symbols-outlined text-5xl text-slate-200 mb-3 block">leaderboard</span>
        <p class="font-semibold text-slate-700">Belum ada snapshot untuk periode ini.</p>
    </div>
    @else

    {{-- Ranking --}}
    <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Ranking {{ $months[$month] }} {{ $year }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px] text-[#215aac]">location_on</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Rank Area</p>
                    <p class="text-2xl font-bold font-jakarta text-slate-900">{{ $latest->rank_area ? '#'.$latest->rank_area : '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $latest->area_name_snapshot ?: '—' }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-full bg-violet-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px] text-violet-600">map</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Rank Region</p>
                    <p class="text-2xl font-bold font-jakarta text-slate-900">{{ $latest->rank_region ? '#'.$latest->rank_region : '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $latest->region_name_snapshot ?: '—' }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-full bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px] text-amber-600">public</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Rank Nasional</p>
                    <p class="text-2xl font-bold font-jakarta text-slate-900">{{ $latest->rank_national ? '#'.$latest->rank_national : '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Metrik --}}
    <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Skor & Metrik</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <div class="bg-white rounded-xl border border-[#215aac]/20 shadow-sm p-5 flex items-center gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-full bg-[#215aac]/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px] text-[#215aac]">format_list_numbered</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Skor MPI</p>
                    <p class="text-2xl font-bold font-jakarta text-[#215aac]">{{ number_format((float)$latest->mpi_score, 2) }}</p>
                    <p class="text-xs text-slate-400">50% kelas + 50% omzet</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs text-slate-500 font-medium">Kelas</p>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                        {{ (float)$latest->classes_percentage >= 100 ? 'bg-emerald-50 text-emerald-700' : ((float)$latest->classes_percentage >= 50 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
                        {{ number_format((float)$latest->classes_percentage, 1) }}%
                    </span>
                </div>
                <p class="text-xl font-bold font-jakarta text-slate-900">{{ $latest->classes_actual }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Target: {{ $latest->classes_target }} kelas</p>
                <div class="mt-3 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-[#215aac]" style="width: {{ min(100, (float)$latest->classes_percentage) }}%"></div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs text-slate-500 font-medium">Omzet</p>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                        {{ (float)$latest->registration_value_percentage >= 100 ? 'bg-emerald-50 text-emerald-700' : ((float)$latest->registration_value_percentage >= 50 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
                        {{ number_format((float)$latest->registration_value_percentage, 1) }}%
                    </span>
                </div>
                <p class="text-xl font-bold font-jakarta text-slate-900">Rp {{ number_format((float)$latest->registration_value_actual, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Target: Rp {{ number_format((float)$latest->omzet_target, 0, ',', '.') }}</p>
                <div class="mt-3 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-violet-500" style="width: {{ min(100, (float)$latest->registration_value_percentage) }}%"></div>
                </div>
            </div>

        </div>
    </div>

    {{-- Riwayat snapshot --}}
    @if($history->count() > 1)
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="border-b border-slate-100 px-5 py-4 bg-slate-50/50 rounded-t-xl">
            <h3 class="font-jakarta text-sm font-bold text-slate-800">Riwayat Snapshot Bulan Ini</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">MPI</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Kelas</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Omzet</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-center">Rank Nasional</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($history->reverse() as $snap)
                    <tr class="hover:bg-slate-50/70 transition-colors {{ $loop->first ? 'bg-blue-50/30' : '' }}">
                        <td class="px-5 py-3.5 font-bold text-[#215aac]">{{ number_format((float)$snap->mpi_score, 2) }}</td>
                        <td class="px-5 py-3.5 text-right tabular-nums text-slate-600">
                            {{ $snap->classes_actual }}/{{ $snap->classes_target }}
                            <span class="text-xs text-slate-400">({{ number_format((float)$snap->classes_percentage, 1) }}%)</span>
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums text-slate-600">
                            Rp {{ number_format((float)$snap->registration_value_actual, 0, ',', '.') }}
                            <span class="text-xs text-slate-400">({{ number_format((float)$snap->registration_value_percentage, 1) }}%)</span>
                        </td>
                        <td class="px-5 py-3.5 text-center text-slate-700">#{{ $snap->rank_national }}</td>
                        <td class="px-5 py-3.5">
                            @if($snap->isFinalized())
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-full">Final</span>
                            @elseif($loop->first)
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full">Terbaru</span>
                            @else
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-xs font-semibold rounded-full">Lama</span>
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
