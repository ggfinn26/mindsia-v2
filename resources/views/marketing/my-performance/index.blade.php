@extends('layouts.dashboard')

@section('title', 'Ranking MPI Saya')
@section('header_title', 'Ranking MPI Saya')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $latest = $snapshots->last();
@endphp

<div class="w-full space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-jakarta text-xl font-bold tracking-tight text-slate-900">Prestasi & Ranking MPI Saya</h2>
            <p class="mt-1 text-sm text-slate-500">Skor MPI dan ranking Anda berdasarkan kelas dan pendaftaran member.</p>
        </div>
        @if($latest?->isFinalized())
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-full">
                <span class="material-symbols-outlined text-[14px]">verified</span>
                Final
            </span>
        @elseif($latest)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold rounded-full">
                <span class="material-symbols-outlined text-[14px]">pending</span>
                Sementara (belum final)
            </span>
        @endif
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-4 items-end bg-white p-4 sm:p-5 rounded-xl border border-slate-100 shadow-sm">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Bulan</label>
            <select name="month" class="block w-full min-w-[140px] bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}"
                class="block w-28 bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#215aac] rounded-lg hover:bg-[#194a91] transition-colors">
                Terapkan Filter
            </button>
            @if(request('month') != now()->month || request('year') != now()->year)
                <a href="{{ route('marketing.my-performance') }}" class="px-3 py-2 text-sm text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Reset</a>
            @endif
        </div>
    </form>

    @if($latest)

        {{-- Ranking Cards --}}
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Ranking {{ $months[$month] }} {{ $year }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px] text-[#215aac]">location_on</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium">Rank Area</p>
                        <p class="text-2xl font-bold font-jakarta text-slate-900 leading-tight">
                            {{ $latest->rank_area ? '#'.$latest->rank_area : '—' }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $latest->area_name_snapshot ?? '—' }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full bg-violet-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px] text-violet-600">map</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium">Rank Region</p>
                        <p class="text-2xl font-bold font-jakarta text-slate-900 leading-tight">
                            {{ $latest->rank_region ? '#'.$latest->rank_region : '—' }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $latest->region_name_snapshot ?? '—' }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full bg-amber-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px] text-amber-600">public</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium">Rank Nasional</p>
                        <p class="text-2xl font-bold font-jakarta text-slate-900 leading-tight">
                            {{ $latest->rank_national ? '#'.$latest->rank_national : '—' }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">Seluruh Indonesia</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- MPI Score + Breakdown --}}
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Skor MPI & Metrik</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- MPI Score --}}
                <div class="bg-white rounded-xl border border-[#215aac]/20 shadow-sm p-5 sm:col-span-2 lg:col-span-1 flex items-center gap-4">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full bg-[#215aac]/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px] text-[#215aac]">format_list_numbered</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium">Skor MPI</p>
                        <p class="text-2xl font-bold font-jakarta text-[#215aac] leading-tight">
                            {{ $latest->mpi_score !== null ? number_format((float)$latest->mpi_score, 2) : '—' }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">50% kelas + 50% omzet</p>
                    </div>
                </div>

                {{-- Kelas --}}
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs text-slate-500 font-medium">Kelas</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ (float)$latest->classes_percentage >= 100 ? 'bg-emerald-50 text-emerald-700' : ((float)$latest->classes_percentage >= 50 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
                            {{ number_format((float)$latest->classes_percentage, 1) }}%
                        </span>
                    </div>
                    <p class="text-xl font-bold font-jakarta text-slate-900">{{ $latest->classes_actual ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Target: {{ $latest->classes_target ?? 0 }} kelas</p>
                    <div class="mt-3 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-[#215aac] transition-all"
                            style="width: {{ min(100, (float)$latest->classes_percentage) }}%"></div>
                    </div>
                </div>

                {{-- Omzet --}}
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs text-slate-500 font-medium">Pendaftaran</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ (float)$latest->registration_value_percentage >= 100 ? 'bg-emerald-50 text-emerald-700' : ((float)$latest->registration_value_percentage >= 50 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
                            {{ number_format((float)$latest->registration_value_percentage, 1) }}%
                        </span>
                    </div>
                    <p class="text-xl font-bold font-jakarta text-slate-900">
                        Rp {{ number_format((float)$latest->registration_value_actual, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">Target: Rp {{ number_format((float)$latest->omzet_target, 0, ',', '.') }}</p>
                    <div class="mt-3 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-violet-500 transition-all"
                            style="width: {{ min(100, (float)$latest->registration_value_percentage) }}%"></div>
                    </div>
                </div>

                {{-- Members --}}
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs text-slate-500 font-medium mb-3">Member</p>
                    <div class="flex items-end gap-4">
                        <div>
                            <p class="text-xl font-bold font-jakarta text-slate-900">{{ $latest->fixed_members_count ?? 0 }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Fixed</p>
                        </div>
                        <div class="text-slate-200 font-light text-lg mb-1">/</div>
                        <div>
                            <p class="text-xl font-bold font-jakarta text-slate-500">{{ $latest->prospective_members_count ?? 0 }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Calon</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Snapshot History --}}
        @if($snapshots->count() > 1)
        <div class="bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="border-b border-slate-100 px-5 py-4 flex items-center justify-between bg-slate-50/50 rounded-t-xl">
                <h3 class="font-jakarta text-sm font-bold text-slate-800">Riwayat Snapshot</h3>
                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">{{ $snapshots->count() }} snapshot</span>
            </div>
            <div class="w-full overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap">#</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap">MPI</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap text-center">Rank Area</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap text-center">Rank Nasional</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap text-right">Kelas</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap text-right">Omzet</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($snapshots->reverse() as $snap)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $loop->first ? 'bg-blue-50/30' : '' }}">
                            <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $snap->id }}</td>
                            <td class="px-5 py-3.5 font-bold text-[#215aac]">
                                {{ $snap->mpi_score !== null ? number_format((float)$snap->mpi_score, 2) : '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-center text-slate-700">
                                {{ $snap->rank_area ? '#'.$snap->rank_area : '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-center text-slate-700">
                                {{ $snap->rank_national ? '#'.$snap->rank_national : '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums text-slate-600">
                                {{ $snap->classes_actual ?? 0 }} / {{ $snap->classes_target ?? 0 }}
                                <span class="text-xs text-slate-400">({{ number_format((float)$snap->classes_percentage, 1) }}%)</span>
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums text-slate-600">
                                Rp {{ number_format((float)$snap->registration_value_actual, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($snap->isFinalized())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-full">Final</span>
                                @elseif($loop->first)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full">Terbaru</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 text-slate-500 text-xs font-semibold rounded-full">Lama</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    @else

        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-5 py-20 text-center">
            <span class="material-symbols-outlined text-5xl text-slate-200 mb-3 block">format_list_numbered</span>
            <p class="font-semibold text-slate-700">Belum ada data MPI untuk {{ $months[$month] }} {{ $year }}.</p>
            <p class="text-sm text-slate-400 mt-1">Data akan muncul setelah KPI Anda diproses oleh sistem.</p>
        </div>

    @endif

</div>
@endsection
