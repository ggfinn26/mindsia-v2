@extends('layouts.dashboard')

@section('title', 'Rekap Absensi Pegawai')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-[var(--dash-text-heading)] mb-6">Rekap Absensi Pegawai</h1>

    {{-- Filter --}}
    <form method="GET" class="bg-[var(--dash-card-bg)] shadow rounded-lg p-4 mb-6">
        {{-- preserve sort across filter submissions --}}
        <input type="hidden" name="sort" value="{{ $sort }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-[var(--dash-text-body)] mb-1">Bulan</label>
                <select name="month" data-no-enhance class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border border-[var(--dash-input-border)] rounded-md py-2 px-3 text-sm">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected($m == $month)>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-[var(--dash-text-body)] mb-1">Tahun</label>
                <select name="year" data-no-enhance class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border border-[var(--dash-input-border)] rounded-md py-2 px-3 text-sm">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-[var(--dash-text-body)] mb-1">Cabang</label>
                <select name="branch_id" data-no-enhance class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border border-[var(--dash-input-border)] rounded-md py-2 px-3 text-sm">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branch->id == $branchId)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-[var(--dash-text-body)] mb-1">Area</label>
                <select name="area_id" data-no-enhance class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border border-[var(--dash-input-border)] rounded-md py-2 px-3 text-sm">
                    <option value="">Semua Area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" @selected($area->id == $areaId)>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-[var(--dash-text-body)] mb-1">Cari Pegawai</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama pegawai..."
                    class="block w-full border border-[var(--dash-input-border)] rounded-md py-2 px-3 text-sm">
            </div>
        </div>
        <div class="mt-3 flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-[var(--dash-text-body)] cursor-pointer">
                <input type="checkbox" name="late_only" value="1" @checked($lateOnly) class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] rounded text-blue-600">
                Hanya tampilkan pegawai yang pernah terlambat
            </label>
            <div class="flex gap-2">
                <a href="{{ route('attendance-recaps.index') }}" class="text-xs text-[var(--dash-text-muted)] px-3 py-2 border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] font-semibold inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">restart_alt</span>Reset</a>
                <button type="submit" class="bg-[var(--dash-primary)] hover:opacity-90 text-white font-medium py-2 px-5 rounded-md text-sm inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">filter_list</span>Filter</button>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-[var(--dash-card-bg)] shadow rounded-lg overflow-hidden">
        @if($recaps->isEmpty())
            <div class="text-center py-12 text-[var(--dash-text-faint)]">
                <span class="material-symbols-outlined text-[48px] mb-2 block">how_to_reg</span>
                <p class="font-medium">Belum ada data rekap untuk periode ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                    @php
                        $sortUrl = fn(string $s) => request()->fullUrlWithQuery(['sort' => $s, 'page' => 1]);
                        $sortIcon = fn(string $s) => $sort === $s ? '↓' : '';
                    @endphp
                    <thead class="bg-[var(--dash-page-bg)]">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">
                                <a href="{{ $sortUrl('name') }}" class="hover:text-[var(--dash-text-heading)]">Pegawai {{ $sortIcon('name') }}</a>
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">Cabang</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">
                                <a href="{{ $sortUrl('present') }}" class="hover:text-[var(--dash-text-heading)]">Hadir {{ $sortIcon('present') }}</a>
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">
                                <a href="{{ $sortUrl('absent') }}" class="hover:text-[var(--dash-text-heading)]">Alpha {{ $sortIcon('absent') }}</a>
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">
                                <a href="{{ $sortUrl('late') }}" class="hover:text-[var(--dash-text-heading)]">Telat {{ $sortIcon('late') }}</a>
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">Izin/Sakit/Cuti</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">Libur</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">Kehadiran</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-[var(--dash-card-bg)] divide-y divide-[var(--dash-card-border)]">
                        @foreach($recaps as $recap)
                            @php
                                $effective = $recap->total_effective_working_days ?: 1;
                                $pct = min(100, round(($recap->total_present / $effective) * 100));
                            @endphp
                            <tr class="hover:bg-[var(--dash-page-bg)]">
                                <td class="px-5 py-3.5">
                                    <div class="font-medium text-[var(--dash-text-heading)]">{{ $recap->employee->full_name }}</div>
                                    <div class="text-xs text-[var(--dash-text-faint)]">{{ $recap->employee->currentStatus?->position?->position_name ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-[var(--dash-text-body)]">{{ $recap->employee->branch?->branch_name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-center font-medium text-green-700">{{ $recap->total_present }}</td>
                                <td class="px-5 py-3.5 text-center font-medium text-red-600">{{ $recap->total_absent }}</td>
                                <td class="px-5 py-3.5 text-center text-yellow-700">
                                    {{ $recap->total_late }}
                                    @if($recap->total_late_minutes > 0)
                                        <span class="text-xs text-[var(--dash-text-faint)]">({{ $recap->total_late_minutes }}m)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center text-blue-600">
                                    {{ $recap->total_sick + $recap->total_permission + $recap->total_leave }}
                                </td>
                                <td class="px-5 py-3.5 text-center text-[var(--dash-text-muted)]">{{ $recap->total_holiday }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $pct >= 80 ? 'bg-green-100 text-green-800' : ($pct >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('attendance-recaps.show', $recap->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($recaps->hasPages())
                <div class="px-5 py-4 border-t border-[var(--dash-card-border)]">{{ $recaps->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
