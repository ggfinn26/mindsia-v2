@extends('layouts.dashboard')

@section('title', 'KPI Snapshot & Ranking Marketing')
@section('header_title', 'KPI Snapshot & Ranking')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Sales & Service</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">KPI Snapshot & Ranking Marketing</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Ranking MPI marketing berdasarkan kelas aktual dan omzet bulan ini.</p>
        </div>
        @can('marketing.kpi.view')
        <form method="POST" action="{{ route('marketing-kpi.finalize') }}" class="shrink-0">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit"
                onclick="return confirm('Finalize snapshot {{ $month }}/{{ $year }}? Data tidak bisa diubah setelah ini.')"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                style="background-color: #059669;">
                <span class="material-symbols-outlined text-[16px]">lock</span>
                Finalize Periode
            </button>
        </form>
        @endcan
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Bulan</label>
            <select name="month"
                    class="block min-w-[140px] rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}"
                   class="block w-28 rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
        </div>
        @if($canViewNational)
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Area</label>
            <select name="area_id"
                    class="block min-w-[160px] rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Area</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" @selected($areaId == $area->id)>{{ $area->area_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Cabang</label>
            <select name="branch_id"
                    class="block min-w-[160px] rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->branch_name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity inline-flex items-center gap-1.5"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Terapkan Filter
            </button>
            @if(request()->hasAny(['month','year','branch_id','area_id']))
                <a href="{{ route('marketing-kpi.index') }}"
                   class="px-3 py-2 text-sm font-semibold rounded-lg transition-colors inline-flex items-center gap-1.5"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Ranking {{ $months[$month] }} {{ $year }}</h3>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                  style="background-color: var(--dash-card-border); color: var(--dash-text-muted);">{{ $snapshots->count() }} marketing</span>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide w-16" style="color: var(--dash-text-muted);">Rank</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Cabang</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Skor MPI</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Kelas</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Target Omzet</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Omzet</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($snapshots as $snap)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-4">
                            @if($snap->rank_national <= 3)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                    {{ $snap->rank_national === 1 ? 'bg-amber-100 text-amber-700' : ($snap->rank_national === 2 ? 'bg-slate-200 text-[color:var(--dash-text-body)]' : 'bg-orange-100 text-orange-600') }}">
                                    {{ $snap->rank_national }}
                                </span>
                            @else
                                <span class="font-medium pl-2" style="color: var(--dash-text-muted);">{{ $snap->rank_national }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-medium" style="color: var(--dash-text-heading);">{{ $snap->employee_name_snapshot }}</p>
                        </td>
                        <td class="px-5 py-4" style="color: var(--dash-text-muted);">{{ $snap->branch_name_snapshot }}</td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-bold tabular-nums" style="color: var(--dash-primary);">{{ number_format((float)$snap->mpi_score, 2) }}</span>
                        </td>
                        <td class="px-5 py-4 text-right tabular-nums" style="color: var(--dash-text-body);">
                            {{ $snap->classes_actual }}/{{ $snap->classes_target }}
                            <span class="text-xs" style="color: var(--dash-text-faint);">({{ number_format((float)$snap->classes_percentage, 1) }}%)</span>
                        </td>
                        <td class="px-5 py-4 text-right tabular-nums" style="color: var(--dash-text-faint);">
                            Rp {{ number_format((float)$snap->omzet_target, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-right tabular-nums" style="color: var(--dash-text-body);">
                            Rp {{ number_format((float)$snap->registration_value_actual, 0, ',', '.') }}
                            <span class="text-xs" style="color: var(--dash-text-faint);">({{ number_format((float)$snap->registration_value_percentage, 1) }}%)</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($snap->isFinalized())
                                <span class="inline-flex px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">Final</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Sementara</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($snap->employee)
                            <a href="{{ route('marketing-kpi.show', [$snap->employee, 'month' => $month, 'year' => $year]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                               <span class="material-symbols-outlined text-[14px]">visibility</span>Detail
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-20 text-center">
                            <span class="material-symbols-outlined text-5xl mb-3 block" style="color: var(--dash-text-faint);">leaderboard</span>
                            <p class="font-semibold" style="color: var(--dash-text-heading);">Belum ada snapshot KPI untuk {{ $months[$month] }} {{ $year }}.</p>
                            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Data muncul otomatis saat pembayaran member dikonfirmasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
