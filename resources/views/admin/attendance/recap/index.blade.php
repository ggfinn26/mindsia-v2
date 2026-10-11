@extends('layouts.dashboard')

@section('title', 'Rekap Absensi Pegawai - MINDSIA')
@section('header_title', 'Rekap Absensi Pegawai')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">

    {{-- Page Header (Pola A) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Presensi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Rekap Absensi Pegawai</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Rekapitulasi kehadiran bulanan pegawai seluruh cabang dan area.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('work-attendance.manage') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors shrink-0"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[18px]">fact_check</span>
                Verifikasi & Koreksi
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="rounded-2xl p-5"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" class="space-y-4">
            {{-- preserve sort across filter submissions --}}
            <input type="hidden" name="sort" value="{{ $sort }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                           style="color: var(--dash-text-muted);">Bulan</label>
                    <select name="month" data-no-enhance
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" @selected($m == $month)>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                           style="color: var(--dash-text-muted);">Tahun</label>
                    <select name="year" data-no-enhance
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';">
                        @foreach(range(now()->year - 1, now()->year + 1) as $y)
                            <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                           style="color: var(--dash-text-muted);">Cabang</label>
                    <select name="branch_id" data-no-enhance
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected($branch->id == $branchId)>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                           style="color: var(--dash-text-muted);">Area</label>
                    <select name="area_id" data-no-enhance
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';">
                        <option value="">Semua Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" @selected($area->id == $areaId)>{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                           style="color: var(--dash-text-muted);">Cari Pegawai</label>
                    <div class="relative w-full">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[16px]"
                              style="color: var(--dash-text-muted);">search</span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Nama pegawai..."
                               class="w-full py-2 pr-3 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading); padding-left: 2.5rem !important;"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';">
                    </div>
                </div>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t"
                 style="border-color: var(--dash-card-border);">
                <label class="inline-flex items-center gap-2 text-sm cursor-pointer select-none"
                       style="color: var(--dash-text-body);">
                    <input type="checkbox" name="late_only" value="1" @checked($lateOnly)
                           class="rounded focus:ring-0 cursor-pointer"
                           style="accent-color: var(--dash-primary);">
                    <span>Hanya tampilkan pegawai yang pernah terlambat</span>
                </label>
                <div class="flex items-center gap-2">
                    <a href="{{ route('attendance-recaps.index') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors"
                       style="color: var(--dash-text-muted); background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                        Reset
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @if($recaps->isEmpty())
            <div class="text-center py-16 px-4">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-2"
                      style="color: var(--dash-text-faint);">how_to_reg</span>
                <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                    Belum ada data rekap untuk periode ini.
                </p>
                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">
                    Coba ubah bulan, tahun, atau filter pencarian lainnya.
                </p>
            </div>
        @else
            @php
                $sortUrl = fn(string $s) => request()->fullUrlWithQuery(['sort' => $s, 'page' => 1]);
                $sortIcon = fn(string $s) => $sort === $s ? 'arrow_downward' : 'unfold_more';
            @endphp
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">
                                <a href="{{ $sortUrl('name') }}" class="inline-flex items-center gap-1 group transition-colors"
                                   style="color: var(--dash-text-muted);">
                                    <span>Pegawai</span>
                                    <span class="material-symbols-outlined text-[14px]">{{ $sortIcon('name') }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Cabang</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">
                                <a href="{{ $sortUrl('present') }}" class="inline-flex items-center justify-center gap-1 group transition-colors"
                                   style="color: var(--dash-text-muted);">
                                    <span>Hadir</span>
                                    <span class="material-symbols-outlined text-[14px]">{{ $sortIcon('present') }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">
                                <a href="{{ $sortUrl('absent') }}" class="inline-flex items-center justify-center gap-1 group transition-colors"
                                   style="color: var(--dash-text-muted);">
                                    <span>Alpha</span>
                                    <span class="material-symbols-outlined text-[14px]">{{ $sortIcon('absent') }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">
                                <a href="{{ $sortUrl('late') }}" class="inline-flex items-center justify-center gap-1 group transition-colors"
                                   style="color: var(--dash-text-muted);">
                                    <span>Telat</span>
                                    <span class="material-symbols-outlined text-[14px]">{{ $sortIcon('late') }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Izin / Sakit / Cuti</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Libur</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Kehadiran</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recaps as $recap)
                            @php
                                $effective = $recap->total_effective_working_days ?: 1;
                                $pct = min(100, round(($recap->total_present / $effective) * 100));
                                $pctBadge = $pct >= 80
                                    ? ['bg' => 'var(--dash-status-success-bg)', 'text' => 'var(--dash-status-success-text)']
                                    : ($pct >= 60
                                        ? ['bg' => 'var(--dash-status-warning-bg)', 'text' => 'var(--dash-status-warning-text)']
                                        : ['bg' => 'var(--dash-status-danger-bg)', 'text' => 'var(--dash-status-danger-text)']);
                            @endphp
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                class="transition-colors"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                                        {{ $recap->employee->full_name }}
                                    </div>
                                    <div class="text-xs mt-0.5" style="color: var(--dash-text-faint);">
                                        {{ $recap->employee->currentStatus?->position?->position_name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm" style="color: var(--dash-text-body);">
                                    {{ $recap->employee->branch?->branch_name ?? '-' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center text-sm font-bold font-mono"
                                    style="color: var(--dash-status-success-text);">
                                    {{ $recap->total_present }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center text-sm font-bold font-mono"
                                    style="color: var(--dash-status-danger-text);">
                                    {{ $recap->total_absent }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center text-sm font-semibold font-mono"
                                    style="color: var(--dash-status-warning-text);">
                                    {{ $recap->total_late }}
                                    @if($recap->total_late_minutes > 0)
                                        <span class="text-xs font-normal ml-0.5" style="color: var(--dash-text-faint);">
                                            ({{ $recap->total_late_minutes }}m)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center text-sm font-semibold font-mono"
                                    style="color: var(--dash-status-info-text);">
                                    {{ $recap->total_sick + $recap->total_permission + $recap->total_leave }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center text-sm font-mono"
                                    style="color: var(--dash-text-muted);">
                                    {{ $recap->total_holiday }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                          style="background-color: {{ $pctBadge['bg'] }}; color: {{ $pctBadge['text'] }};">
                                        {{ $pct }}%
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('attendance-recaps.show', $recap->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-opacity hover:opacity-90"
                                       style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-primary-bg);">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($recaps->hasPages())
                <div class="px-6 py-4 border-t" style="border-color: var(--dash-card-border);">
                    {{ $recaps->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
