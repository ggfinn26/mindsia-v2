@extends('layouts.dashboard')

@section('title', 'Riwayat Absensi Saya - MINDSIA')
@section('header_title', 'Riwayat Absensi Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6">

    {{-- Page Header + Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Personal Dashboard</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Riwayat Absensi Saya</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                @if($month)
                    Log harian presensi {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}.
                @else
                    Ringkasan kehadiran per bulan untuk tahun {{ $year }}.
                @endif
            </p>
        </div>

        {{-- Filter bar & Export PDF --}}
        <form method="GET" class="flex flex-wrap items-end gap-2.5">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1"
                       style="color: var(--dash-text-faint);">Tahun</label>
                <select name="year" data-no-enhance
                        class="px-3 py-2 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'"
                        onchange="this.form.submit()">
                    @foreach(range(now()->year - 2, now()->year) as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1"
                       style="color: var(--dash-text-faint);">Bulan</label>
                <select name="month" data-no-enhance
                        class="px-3 py-2 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'"
                        onchange="this.form.submit()">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected($m == $month)>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- PDF dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1 opacity-0 select-none">&nbsp;</label>
                <button type="button" @click="open = !open"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary);">picture_as_pdf</span>
                    Export PDF
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-muted);">expand_more</span>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute right-0 mt-1 w-48 rounded-xl shadow-lg z-20 py-1"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <a href="{{ route('work-attendance.my-recap.pdf', ['year' => $year, 'month' => $month ?: now()->month, 'range' => 'monthly']) }}"
                       target="_blank"
                       class="flex items-center gap-2.5 px-4 py-2 text-sm font-medium transition-colors"
                       style="color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary);">calendar_today</span>
                        Bulan ini
                    </a>
                    <a href="{{ route('work-attendance.my-recap.pdf', ['year' => $year, 'month' => $month ?: now()->month, 'range' => 'semester']) }}"
                       target="_blank"
                       class="flex items-center gap-2.5 px-4 py-2 text-sm font-medium transition-colors"
                       style="color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary);">date_range</span>
                        Semester
                    </a>
                    <a href="{{ route('work-attendance.my-recap.pdf', ['year' => $year, 'range' => 'annual']) }}"
                       target="_blank"
                       class="flex items-center gap-2.5 px-4 py-2 text-sm font-medium transition-colors"
                       style="color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary);">event_note</span>
                        Tahunan ({{ $year }})
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    @if($recaps->isEmpty())
        <div class="rounded-2xl px-5 py-16 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
                  style="color: var(--dash-text-faint);">history_toggle_off</span>
            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada rekap presensi</p>
            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">
                Data rekap presensi untuk tahun {{ $year }} belum tersedia.
            </p>
        </div>
    @else

        {{-- Summary cards per bulan --}}
        <div class="space-y-3">
            @foreach($recaps as $recap)
                @php
                    $effective  = $recap->total_effective_working_days ?: 1;
                    $pct        = min(100, round(($recap->total_present / $effective) * 100));
                    $pctColor   = $pct >= 80 ? '#10b981' : ($pct >= 60 ? '#f59e0b' : '#f43f5e');
                    $badgeStyle = $pct >= 80
                        ? 'background-color: #f0fdf4; color: #059669; border: 1px solid #bbf7d0;'
                        : ($pct >= 60
                            ? 'background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;'
                            : 'background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca;');
                    $isActive   = $month && $month == $recap->period_month;
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['month' => $recap->period_month, 'sort' => $sort]) }}"
                   class="block rounded-2xl px-5 py-4 transition-all"
                   style="background-color: var(--dash-card-bg); border: 1px solid {{ $isActive ? 'var(--dash-primary)' : 'var(--dash-card-border)' }}; {{ $isActive ? 'box-shadow: 0 0 0 1px var(--dash-primary);' : '' }}"
                   onmouseover="if (!{{ $isActive ? '1' : '0' }}) this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="if (!{{ $isActive ? '1' : '0' }}) this.style.backgroundColor='var(--dash-card-bg)'">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-jakarta text-sm font-bold tracking-tight"
                              style="color: var(--dash-text-heading);">
                            {{ \Carbon\Carbon::create($year, $recap->period_month)->translatedFormat('F Y') }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                              style="{{ $badgeStyle }}">
                            {{ $pct }}% hadir
                        </span>
                    </div>
                    {{-- Progress Bar --}}
                    <div class="w-full rounded-full h-1.5 mb-3"
                         style="background-color: var(--dash-nav-hover-bg);">
                        <div class="h-1.5 rounded-full transition-all"
                             style="width: {{ $pct }}%; background-color: {{ $pctColor }};"></div>
                    </div>
                    {{-- 5 Stat breakdown --}}
                    <div class="grid grid-cols-5 text-center text-xs gap-2 pt-1"
                         style="border-top: 1px solid var(--dash-card-border);">
                        <div>
                            <span class="font-bold text-sm block" style="color: #059669;">{{ $recap->total_present }}</span>
                            <span class="text-[11px] font-medium mt-0.5 block" style="color: var(--dash-text-muted);">Hadir</span>
                        </div>
                        <div>
                            <span class="font-bold text-sm block" style="color: #dc2626;">{{ $recap->total_absent }}</span>
                            <span class="text-[11px] font-medium mt-0.5 block" style="color: var(--dash-text-muted);">Alpha</span>
                        </div>
                        <div>
                            <span class="font-bold text-sm block" style="color: #d97706;">{{ $recap->total_late }}</span>
                            <span class="text-[11px] font-medium mt-0.5 block" style="color: var(--dash-text-muted);">Telat</span>
                        </div>
                        <div>
                            <span class="font-bold text-sm block" style="color: var(--dash-primary);">{{ $recap->total_sick + $recap->total_permission + $recap->total_leave }}</span>
                            <span class="text-[11px] font-medium mt-0.5 block" style="color: var(--dash-text-muted);">Izin/S/C</span>
                        </div>
                        <div>
                            <span class="font-bold text-sm block" style="color: var(--dash-text-faint);">{{ $recap->total_holiday }}</span>
                            <span class="text-[11px] font-medium mt-0.5 block" style="color: var(--dash-text-muted);">Libur</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Daily log table: tampil saat bulan dipilih --}}
        @if($month && $logs !== null)
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                {{-- Table header bar --}}
                <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-jakarta text-sm font-semibold" style="color: var(--dash-text-heading);">
                        Log Harian: {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                    </h2>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="font-medium" style="color: var(--dash-text-muted);">Urutkan:</span>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'date']) }}"
                           class="px-2 py-1 rounded-lg transition-colors {{ $sort === 'date' ? 'font-semibold' : '' }}"
                           style="{{ $sort === 'date' ? 'color: var(--dash-primary); background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);' : 'color: var(--dash-text-muted);' }}"
                           onmouseover="this.style.color='var(--dash-text-heading)'"
                           onmouseout="this.style.color='{{ $sort === 'date' ? 'var(--dash-primary)' : 'var(--dash-text-muted)' }}'">
                            Tanggal
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'late_desc']) }}"
                           class="px-2 py-1 rounded-lg transition-colors {{ $sort === 'late_desc' ? 'font-semibold' : '' }}"
                           style="{{ $sort === 'late_desc' ? 'color: var(--dash-primary); background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);' : 'color: var(--dash-text-muted);' }}"
                           onmouseover="this.style.color='var(--dash-text-heading)'"
                           onmouseout="this.style.color='{{ $sort === 'late_desc' ? 'var(--dash-primary)' : 'var(--dash-text-muted)' }}'">
                            Terlambat &darr;
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'status']) }}"
                           class="px-2 py-1 rounded-lg transition-colors {{ $sort === 'status' ? 'font-semibold' : '' }}"
                           style="{{ $sort === 'status' ? 'color: var(--dash-primary); background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);' : 'color: var(--dash-text-muted);' }}"
                           onmouseover="this.style.color='var(--dash-text-heading)'"
                           onmouseout="this.style.color='{{ $sort === 'status' ? 'var(--dash-primary)' : 'var(--dash-text-muted)' }}'">
                            Status
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Tanggal</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Masuk</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Keluar</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Terlambat</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                @php
                                    $statusMap = [
                                        'present'    => ['Hadir',        'background-color: #f0fdf4; color: #059669; border: 1px solid #bbf7d0;'],
                                        'checked_in' => ['Sedang Hadir', 'background-color: var(--dash-primary-bg); color: var(--dash-primary-text); border: 1px solid color-mix(in srgb, var(--dash-primary) 30%, transparent);'],
                                        'absent'     => ['Alpha',        'background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca;'],
                                        'sick'       => ['Sakit',        'background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;'],
                                        'permission' => ['Izin',         'background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;'],
                                        'leave'      => ['Cuti',         'background-color: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff;'],
                                        'holiday'    => ['Libur',        'background-color: #ccfbf1; color: #0f766e; border: 1px solid #99f6e4;'],
                                    ];
                                    [$label, $badgeStyle] = $statusMap[$log->status] ?? [ucfirst($log->status), 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);'];
                                @endphp
                                <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                                        {{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('d M Y, l') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center" style="color: var(--dash-text-body);">
                                        {{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center" style="color: var(--dash-text-body);">
                                        {{ $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if($log->late_minutes > 0)
                                            <span class="font-semibold" style="color: #d97706;">{{ $log->late_minutes }}m</span>
                                        @else
                                            <span style="color: var(--dash-text-faint);">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="{{ $badgeStyle }}">{{ $label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center">
                                        <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                              style="color: var(--dash-text-faint);">event_busy</span>
                                        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Tidak ada log presensi harian untuk bulan ini.</p>
                                        <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Posisi dengan presensi otomatis tidak menghasilkan log harian.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($logs->hasPages())
                    <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        @endif

    @endif
</div>
@endsection

