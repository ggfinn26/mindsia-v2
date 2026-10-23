@extends('layouts.dashboard')

@section('title', 'KPI Snapshot & Ranking Marketing')
@section('header_title', 'KPI Snapshot & Ranking')

@section('content')
@php
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    $totalMarketing = $snapshots->count();
    $avgMpiScore = $totalMarketing > 0 ? (float) $snapshots->avg('mpi_score') : 0;
    $totalClasses = $snapshots->sum('classes_actual');
    $totalOmzet = $snapshots->sum('registration_value_actual');
@endphp

<div x-data="{ showFinalizeModal: false }" class="space-y-6">
    {{-- Pola A Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="text-xs font-semibold tracking-wider uppercase mb-1" style="color: var(--dash-primary-text)">Pemasaran</div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading)">KPI Snapshot & Ranking Marketing</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted)">Peringkat Marketing Performance Index (MPI) berdasarkan pencapaian kelas dan omzet.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('marketing.kpi.view')
                <a href="{{ route('marketing-kpi.rules') }}" 
                    class="inline-flex items-center gap-1.5 rounded-lg border px-3.5 py-2 text-xs sm:text-sm font-medium transition-colors"
                    style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading)">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    Aturan Ranking
                </a>
                <button type="button" @click="showFinalizeModal = true"
                    class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                    style="background-color: #059669">
                    <span class="material-symbols-outlined text-[18px]">lock</span>
                    Finalize Periode
                </button>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-600 dark:text-emerald-400 rounded-xl">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-2 border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-600 dark:text-rose-400 rounded-xl">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Executive Summary Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Marketing --}}
        <div class="rounded-2xl border p-4 sm:p-5 shadow-sm transition-all"
            style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Marketing Dinilai</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-500/10 text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined text-[18px]">group</span>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-bold tabular-nums" style="color: var(--dash-text-heading)">
                {{ $totalMarketing }}
            </div>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted)">Total personil aktif di snapshot</p>
        </div>

        {{-- Card 2: Rata-rata Skor MPI --}}
        <div class="rounded-2xl border p-4 sm:p-5 shadow-sm transition-all"
            style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Rata-rata Skor MPI</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: color-mix(in srgb, var(--dash-primary) 12%, transparent); color: var(--dash-primary)">
                    <span class="material-symbols-outlined text-[18px]">analytics</span>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-bold tabular-nums" style="color: var(--dash-primary)">
                {{ number_format($avgMpiScore, 2) }}
            </div>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted)">Skala pembobotan 50:50</p>
        </div>

        {{-- Card 3: Total Kelas Aktual --}}
        <div class="rounded-2xl border p-4 sm:p-5 shadow-sm transition-all"
            style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Total Kelas Aktual</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-violet-500/10 text-violet-600 dark:text-violet-400">
                    <span class="material-symbols-outlined text-[18px]">school</span>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-bold tabular-nums" style="color: var(--dash-text-heading)">
                {{ number_format($totalClasses) }}
            </div>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted)">Kelas berhasil dibuka</p>
        </div>

        {{-- Card 4: Total Omzet Aktual --}}
        <div class="rounded-2xl border p-4 sm:p-5 shadow-sm transition-all"
            style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Total Omzet Aktual</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <span class="material-symbols-outlined text-[18px]">monetization_on</span>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-bold tabular-nums text-emerald-600 dark:text-emerald-400">
                Rp {{ number_format($totalOmzet, 0, ',', '.') }}
            </div>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted)">Akumulasi pendaftaran periode ini</p>
        </div>
    </div>

    {{-- Filter Card --}}
    <form method="GET" class="flex flex-wrap gap-4 items-end rounded-2xl border p-4 sm:p-5 shadow-sm"
        style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Bulan</label>
            <select name="month" 
                onfocus="this.style.borderColor='var(--dash-primary)'" 
                onblur="this.style.borderColor='var(--dash-input-border)'"
                class="block w-full min-w-[140px] rounded-lg border px-3 py-2 text-xs sm:text-sm outline-none transition-colors"
                style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}"
                onfocus="this.style.borderColor='var(--dash-primary)'" 
                onblur="this.style.borderColor='var(--dash-input-border)'"
                class="block w-28 rounded-lg border px-3 py-2 text-xs sm:text-sm outline-none transition-colors"
                style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
        </div>
        @if($canViewNational)
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Area</label>
                <select name="area_id" 
                    onfocus="this.style.borderColor='var(--dash-primary)'" 
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    class="block w-full min-w-[160px] rounded-lg border px-3 py-2 text-xs sm:text-sm outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
                    <option value="">Semua Area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" @selected($areaId == $area->id)>{{ $area->area_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Cabang</label>
                <select name="branch_id" 
                    onfocus="this.style.borderColor='var(--dash-primary)'" 
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    class="block w-full min-w-[160px] rounded-lg border px-3 py-2 text-xs sm:text-sm outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="flex items-center gap-2">
            <button type="submit" 
                class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-xs sm:text-sm font-semibold shadow-sm transition-opacity hover:opacity-90"
                style="background-color: var(--dash-primary); color: #ffffff">
                <span class="material-symbols-outlined text-[16px] mr-1">filter_alt</span>
                Terapkan Filter
            </button>
            @if(request()->hasAny(['month','year','branch_id','area_id']))
                <a href="{{ route('marketing-kpi.index') }}" 
                    class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-xs sm:text-sm font-medium transition-colors"
                    style="border-color: var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg)">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Table Card with Zero Horizontal Scroll --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
        style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
        <div class="flex items-center justify-between border-b px-5 py-4"
            style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg)">
            <h2 class="text-sm font-bold" style="color: var(--dash-text-heading)">
                Peringkat Kinerja {{ $months[$month] }} {{ $year }}
            </h2>
            <span class="rounded-full border px-2.5 py-1 text-xs font-semibold"
                style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted)">
                {{ $snapshots->count() }} marketing
            </span>
        </div>
        <div class="w-full">
            <table class="w-full text-sm text-left">
                <thead class="border-b text-[11px] font-semibold uppercase tracking-wider"
                    style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted)">
                    <tr>
                        <th class="px-5 py-3.5 w-20 text-center">Rank</th>
                        <th class="px-5 py-3.5">Marketing & Cabang</th>
                        <th class="px-5 py-3.5 text-center">Skor MPI & Status</th>
                        <th class="px-5 py-3.5 text-right">Realisasi Kelas</th>
                        <th class="px-5 py-3.5 text-right">Realisasi Omzet</th>
                        <th class="px-5 py-3.5 text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border)">
                    @forelse($snapshots as $i => $snap)
                    <tr onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'" onmouseout="this.style.backgroundColor='transparent'" class="transition-colors">
                        <td class="px-5 py-3.5 text-center">
                            @if($snap->rank_national === 1)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full font-bold text-xs bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                    1
                                </span>
                            @elseif($snap->rank_national === 2)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full font-bold text-xs bg-slate-500/20 text-slate-700 dark:text-slate-300 border border-slate-500/30">
                                    2
                                </span>
                            @elseif($snap->rank_national === 3)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full font-bold text-xs bg-orange-500/20 text-orange-600 dark:text-orange-400 border border-orange-500/30">
                                    3
                                </span>
                            @else
                                <span class="text-xs font-semibold" style="color: var(--dash-text-muted)">#{{ $snap->rank_national }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-semibold" style="color: var(--dash-text-heading)">{{ $snap->employee_name_snapshot }}</div>
                            <div class="text-xs mt-0.5" style="color: var(--dash-text-muted)">{{ $snap->branch_name_snapshot }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="font-bold tabular-nums text-base" style="color: var(--dash-primary)">
                                {{ number_format((float)$snap->mpi_score, 2) }}
                            </div>
                            <div class="mt-1">
                                @if($snap->isFinalized())
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        Final
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        Sementara
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums">
                            <div class="font-medium" style="color: var(--dash-text-heading)">
                                {{ $snap->classes_actual }} / {{ $snap->classes_target }} kelas
                            </div>
                            <div class="text-xs mt-0.5" style="color: var(--dash-text-muted)">
                                {{ number_format((float)$snap->classes_percentage, 1) }}% dari target
                            </div>
                            <div class="mt-1.5 h-1.5 w-full bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden ml-auto max-w-[120px]">
                                <div class="h-full rounded-full transition-all" style="width: {{ min(100, (float)$snap->classes_percentage) }}%; background-color: var(--dash-primary)"></div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums">
                            <div class="font-medium" style="color: var(--dash-text-heading)">
                                Rp {{ number_format((float)$snap->registration_value_actual, 0, ',', '.') }}
                            </div>
                            <div class="text-xs mt-0.5" style="color: var(--dash-text-muted)">
                                Target: Rp {{ number_format((float)$snap->omzet_target, 0, ',', '.') }} ({{ number_format((float)$snap->registration_value_percentage, 1) }}%)
                            </div>
                            <div class="mt-1.5 h-1.5 w-full bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden ml-auto max-w-[120px]">
                                <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ min(100, (float)$snap->registration_value_percentage) }}%"></div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($snap->employee)
                                <a href="{{ route('marketing-kpi.show', [$snap->employee, 'month' => $month, 'year' => $year]) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg border transition-colors hover:opacity-90"
                                   style="border-color: var(--dash-card-border); color: var(--dash-text-heading); background-color: var(--dash-card-bg)">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    Detail
                                </a>
                            @else
                                <span class="text-xs" style="color: var(--dash-text-muted)">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <span class="material-symbols-outlined text-5xl opacity-40 mb-3 block" style="color: var(--dash-text-faint)">leaderboard</span>
                            <p class="font-semibold" style="color: var(--dash-text-heading)">Belum ada snapshot KPI untuk {{ $months[$month] }} {{ $year }}.</p>
                            <p class="text-xs sm:text-sm mt-1" style="color: var(--dash-text-muted)">Data muncul otomatis saat pembayaran member dikonfirmasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Finalize Alpine Modal (tanpa overflow-hidden di card container) --}}
    @can('marketing.kpi.view')
        <div x-show="showFinalizeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div @click.away="showFinalizeModal = false" class="w-full max-w-md rounded-2xl border shadow-xl"
                style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
                <div class="px-6 py-5 border-b rounded-t-2xl flex items-center gap-3"
                    style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg)">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <span class="material-symbols-outlined text-xl">lock</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold" style="color: var(--dash-text-heading)">Finalize Snapshot Periode</h3>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted)">Periode {{ $months[$month] }} {{ $year }}</p>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm leading-relaxed" style="color: var(--dash-text-body)">
                        Apakah Anda yakin ingin memfinalisasi snapshot KPI untuk periode <strong style="color: var(--dash-text-heading)">{{ $months[$month] }} {{ $year }}</strong>?
                    </p>
                    <p class="mt-2 text-xs text-amber-600 dark:text-amber-400 font-medium">
                        Peringatan: Data snapshot yang telah difinalisasi tidak dapat diubah kembali.
                    </p>
                </div>
                <div class="px-6 py-4 border-t rounded-b-2xl flex items-center justify-end gap-3"
                    style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg)">
                    <button type="button" @click="showFinalizeModal = false" 
                        class="px-4 py-2 text-xs sm:text-sm font-medium rounded-lg border transition-colors"
                        style="border-color: var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg)">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('marketing-kpi.finalize') }}" class="inline">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" 
                            class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg text-white shadow-sm transition-opacity hover:opacity-90"
                            style="background-color: #059669">
                            Ya, Finalize Periode
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endcan
</div>
@endsection
