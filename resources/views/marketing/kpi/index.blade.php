@extends('layouts.dashboard')

@section('title', 'KPI Snapshot & Ranking Marketing')
@section('header_title', 'KPI Snapshot & Ranking')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="w-full space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-jakarta text-xl font-bold tracking-tight text-slate-900">KPI Snapshot & Ranking Marketing</h2>
            <p class="mt-1 text-sm text-slate-500">Ranking MPI marketing berdasarkan kelas aktual dan omzet bulan ini.</p>
        </div>
        @can('marketing.kpi.view')
        <form method="POST" action="{{ route('marketing-kpi.finalize') }}">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit"
                onclick="return confirm('Finalize snapshot {{ $month }}/{{ $year }}? Data tidak bisa diubah setelah ini.')"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                <span class="material-symbols-outlined text-[16px]">lock</span>
                Finalize Periode
            </button>
        </form>
        @endcan
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 rounded-xl">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filters --}}
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
        @if($canViewNational)
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Area</label>
            <select name="area_id" class="block w-full min-w-[160px] bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                <option value="">Semua Area</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" @selected($areaId == $area->id)>{{ $area->area_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Cabang</label>
            <select name="branch_id" class="block w-full min-w-[160px] bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->branch_name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#215aac] rounded-lg hover:bg-[#194a91] transition-colors">
                Terapkan Filter
            </button>
            @if(request()->hasAny(['month','year','branch_id','area_id']))
                <a href="{{ route('marketing-kpi.index') }}" class="px-3 py-2 text-sm text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Reset</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="border-b border-slate-100 px-5 py-4 flex items-center justify-between bg-slate-50/50 rounded-t-xl">
            <h3 class="font-jakarta text-sm font-bold text-slate-800">Ranking {{ $months[$month] }} {{ $year }}</h3>
            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">{{ $snapshots->count() }} marketing</span>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide w-16">Rank</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Pegawai</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Cabang</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Skor MPI</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Kelas</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Omzet</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($snapshots as $i => $snap)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-4">
                            @if($snap->rank_national <= 3)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                    {{ $snap->rank_national === 1 ? 'bg-amber-100 text-amber-700' : ($snap->rank_national === 2 ? 'bg-slate-100 text-slate-600' : 'bg-orange-50 text-orange-600') }}">
                                    {{ $snap->rank_national }}
                                </span>
                            @else
                                <span class="text-slate-500 font-medium pl-2">{{ $snap->rank_national }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-900">{{ $snap->employee_name_snapshot }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-500">{{ $snap->branch_name_snapshot }}</td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-bold tabular-nums text-[#215aac]">{{ number_format((float)$snap->mpi_score, 2) }}</span>
                        </td>
                        <td class="px-5 py-4 text-right tabular-nums text-slate-600">
                            {{ $snap->classes_actual }}/{{ $snap->classes_target }}
                            <span class="text-xs text-slate-400">({{ number_format((float)$snap->classes_percentage, 1) }}%)</span>
                        </td>
                        <td class="px-5 py-4 text-right tabular-nums text-slate-600">
                            Rp {{ number_format((float)$snap->registration_value_actual, 0, ',', '.') }}
                            <span class="text-xs text-slate-400">({{ number_format((float)$snap->registration_value_percentage, 1) }}%)</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($snap->isFinalized())
                                <span class="inline-flex px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-full">Final</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full">Sementara</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($snap->employee)
                            <a href="{{ route('marketing-kpi.show', [$snap->employee, 'month' => $month, 'year' => $year]) }}"
                               class="text-[#215aac] text-xs font-medium hover:underline">Detail</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-20 text-center">
                            <span class="material-symbols-outlined text-5xl text-slate-200 mb-3 block">leaderboard</span>
                            <p class="font-semibold text-slate-700">Belum ada snapshot KPI untuk {{ $months[$month] }} {{ $year }}.</p>
                            <p class="text-sm text-slate-400 mt-1">Data muncul otomatis saat pembayaran member dikonfirmasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
