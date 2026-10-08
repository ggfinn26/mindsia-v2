@extends('layouts.dashboard')

@section('title', 'Riwayat Absensi Saya - MINDSIA')
@section('header_title', 'Riwayat Absensi Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Filter bar --}}
    <form method="GET" class="flex flex-wrap items-end gap-3 px-5 py-4 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <input type="hidden" name="sort" value="{{ $sort }}">

        <div>
            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-muted);">Tahun</label>
            <select name="year" onchange="this.form.submit()" class="block w-24">
                @foreach(range(now()->year - 2, now()->year) as $y)
                    <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-muted);">Bulan</label>
            <select name="month" data-with-semua onchange="this.form.submit()" class="block min-w-[140px]">
                <option value="">Semua Bulan</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" @selected($m == $month)>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Export PDF --}}
        <div x-data="{ open: false }" class="relative ml-auto">
            <label class="block text-[11px] mb-1.5">&nbsp;</label>
            <button type="button" @click="open = !open"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold transition-colors"
                    style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-primary); color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-[17px]">picture_as_pdf</span>
                Export PDF
                <span class="material-symbols-outlined text-[17px]">expand_more</span>
            </button>
            <div x-show="open" @click.outside="open = false" x-cloak
                 class="absolute right-0 mt-1 w-48 rounded-2xl shadow-lg z-20 overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <a href="{{ route('work-attendance.my-recap.pdf', ['year' => $year, 'month' => $month ?: now()->month, 'range' => 'monthly']) }}"
                   target="_blank"
                   class="flex items-center gap-2 px-4 py-2.5 text-sm transition-colors"
                   style="color: var(--dash-text-body);">
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary);">calendar_today</span>
                    Bulan ini
                </a>
                <a href="{{ route('work-attendance.my-recap.pdf', ['year' => $year, 'month' => $month ?: now()->month, 'range' => 'semester']) }}"
                   target="_blank"
                   class="flex items-center gap-2 px-4 py-2.5 text-sm transition-colors"
                   style="color: var(--dash-text-body);">
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary);">date_range</span>
                    Semester
                </a>
                <a href="{{ route('work-attendance.my-recap.pdf', ['year' => $year, 'range' => 'annual']) }}"
                   target="_blank"
                   class="flex items-center gap-2 px-4 py-2.5 text-sm transition-colors"
                   style="color: var(--dash-text-body);">
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary);">event_note</span>
                    Tahunan ({{ $year }})
                </a>
            </div>
        </div>
    </form>

    @if($recaps->isEmpty())
        <div class="rounded-2xl py-20 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-4xl" style="color: var(--dash-text-faint);">event_busy</span>
            <p class="mt-2 font-semibold text-sm" style="color: var(--dash-text-heading);">Belum ada rekap</p>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Data rekap absensi untuk tahun {{ $year }} belum tersedia.</p>
        </div>
    @else

        {{-- Monthly summary cards --}}
        <div class="space-y-2">
            @foreach($recaps as $recap)
                @php
                    $effective  = $recap->total_effective_working_days ?: 1;
                    $pct        = min(100, round(($recap->total_present / $effective) * 100));
                    $isActive   = $month && $month == $recap->period_month;
                    $pctBarColor = $pct >= 80 ? '#059669' : ($pct >= 60 ? '#d97706' : '#dc2626');
                    $badgeBg    = $pct >= 80 ? '#f0fdf4' : ($pct >= 60 ? '#fef3c7' : '#fee2e2');
                    $badgeText  = $pct >= 80 ? '#059669' : ($pct >= 60 ? '#d97706' : '#dc2626');
                    $badgeBorder= $pct >= 80 ? '#bbf7d0' : ($pct >= 60 ? '#fde68a' : '#fca5a5');
                    $totalIzin  = $recap->total_sick + $recap->total_permission + $recap->total_leave;
                @endphp
                <div class="px-5 py-4 rounded-2xl"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                            {{ \Carbon\Carbon::create($year, $recap->period_month)->translatedFormat('F Y') }}
                        </span>
                        <div class="flex items-center gap-3">
                            <span class="text-xs" style="color: var(--dash-text-muted);">
                                {{ $recap->total_present }} / {{ $recap->total_effective_working_days }} hari efektif
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold"
                                  style="background-color: {{ $badgeBg }}; color: {{ $badgeText }}; border: 1px solid {{ $badgeBorder }};">
                                {{ $pct }}% hadir
                            </span>
                        </div>
                    </div>

                    <div class="w-full h-1.5 rounded-full mb-3" style="background-color: var(--dash-card-border);">
                        <div class="h-1.5 rounded-full transition-all" style="width: {{ $pct }}%; background-color: {{ $pctBarColor }};"></div>
                    </div>

                    <div class="grid grid-cols-6 text-center text-xs gap-1">
                        <div>
                            <span class="font-bold text-base font-jakarta" style="color: #059669;">{{ $recap->total_present }}</span>
                            <div style="color: var(--dash-text-muted);">Hadir</div>
                        </div>
                        <div>
                            <span class="font-bold text-base font-jakarta" style="color: #dc2626;">{{ $recap->total_absent }}</span>
                            <div style="color: var(--dash-text-muted);">Alpha</div>
                        </div>
                        <div>
                            <span class="font-bold text-base font-jakarta" style="color: #d97706;">{{ $recap->total_late }}</span>
                            <div style="color: var(--dash-text-muted);">Telat</div>
                        </div>
                        <div>
                            <span class="font-bold text-base font-jakarta" style="color: var(--dash-primary);">{{ $totalIzin }}</span>
                            <div style="color: var(--dash-text-muted);">Izin/S/C</div>
                        </div>
                        <div>
                            <span class="font-bold text-base font-jakarta" style="color: var(--dash-text-faint);">{{ $recap->total_holiday }}</span>
                            <div style="color: var(--dash-text-muted);">Libur</div>
                        </div>
                        <div>
                            <span class="font-bold text-base font-jakarta" style="color: var(--dash-text-faint);">{{ $recap->total_effective_working_days }}</span>
                            <div style="color: var(--dash-text-muted);">Efektif</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Daily log --}}
        @if($month && $logs !== null)
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                <div class="px-5 py-4 border-b flex items-center justify-between"
                     style="border-color: var(--dash-card-border);">
                    <div class="flex items-center gap-2.5">
                        <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                        <h2 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">
                            Log Harian — {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                        </h2>
                    </div>
                    <div class="flex gap-3 text-xs">
                        <span style="color: var(--dash-text-muted);">Sort:</span>
                        @foreach(['date' => 'Tanggal', 'late_desc' => 'Terlambat ↓', 'status' => 'Status'] as $key => $label)
                            <a href="{{ request()->fullUrlWithQuery(['sort' => $key]) }}"
                               class="font-medium transition-colors"
                               style="color: {{ $sort === $key ? 'var(--dash-primary)' : 'var(--dash-text-muted)' }};">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>

                @if($logs->isEmpty())
                    <div class="py-10 px-6 flex items-start gap-3"
                         style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-primary-bg);">
                        <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5" style="color: var(--dash-primary);">info</span>
                        <div>
                            <p class="text-sm font-medium" style="color: var(--dash-text-heading);">Tidak ada log check-in/check-out untuk bulan ini.</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Data kehadiran Anda dihitung otomatis oleh sistem berdasarkan jadwal, bukan dari log check-in manual. Rekap bulanan di atas sudah mencerminkan kehadiran Anda.</p>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Tanggal</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Masuk</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Keluar</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Terlambat</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    @php
                                        $statusMap = [
                                            'present'    => ['Hadir',        '#f0fdf4', '#059669', '#bbf7d0'],
                                            'checked_in' => ['Sedang Hadir', 'var(--dash-primary-bg)', 'var(--dash-primary-text)', 'var(--dash-primary)'],
                                            'absent'     => ['Alpha',        '#fee2e2', '#dc2626', '#fca5a5'],
                                            'sick'       => ['Sakit',        '#fef3c7', '#d97706', '#fde68a'],
                                            'permission' => ['Izin',         '#eff6ff', '#2563eb', '#bfdbfe'],
                                            'leave'      => ['Cuti',         '#f5f3ff', '#7c3aed', '#ddd6fe'],
                                            'holiday'    => ['Libur',        '#f0fdfa', '#0d9488', '#99f6e4'],
                                        ];
                                        [$label, $bg, $color, $border] = $statusMap[$log->status] ?? [ucfirst($log->status), 'var(--dash-card-border)', 'var(--dash-text-muted)', 'var(--dash-card-border)'];
                                    @endphp
                                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                        <td class="px-5 py-3 font-medium" style="color: var(--dash-text-heading);">
                                            {{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('d M, l') }}
                                        </td>
                                        <td class="px-5 py-3 text-center" style="color: var(--dash-text-body);">
                                            {{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-5 py-3 text-center" style="color: var(--dash-text-body);">
                                            {{ $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            @if($log->late_minutes > 0)
                                                <span class="font-semibold" style="color: #d97706;">{{ $log->late_minutes }}m</span>
                                            @else
                                                <span style="color: var(--dash-text-faint);">-</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold"
                                                  style="background-color: {{ $bg }}; color: {{ $color }}; border: 1px solid {{ $border }};">
                                                {{ $label }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($logs->hasPages())
                        <div class="px-5 py-4 border-t" style="border-color: var(--dash-card-border);">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </div>
        @endif

    @endif
</div>
@endsection
