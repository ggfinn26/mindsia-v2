@extends('layouts.dashboard')

@section('title', 'Detail Rekap Absensi Pegawai - MINDSIA')
@section('header_title', 'Rekap Absensi Pegawai')

@push('head-scripts')
<style>
@media print {
    aside, header, .no-print { display: none !important; }
    main { padding: 0 !important; overflow: visible !important; }
}
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div class="space-y-4 no-print">
        <div>
            <a href="{{ route('attendance-recaps.index') }}" class="dash-back">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali ke Rekap Absensi Pegawai
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1"
                   style="color: var(--dash-primary-text);">Detail Presensi</p>
                <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                    style="color: var(--dash-text-heading);">
                    Detail Rekap - {{ $recap->employee->full_name }}
                </h1>
                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                    {{ \Carbon\Carbon::create($recap->period_year, $recap->period_month)->translatedFormat('F Y') }}
                    &nbsp;·&nbsp; {{ $recap->employee->branch?->branch_name ?? '-' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="color: var(--dash-text-body); background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Cetak / PDF
                </button>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    @php
        $effective = $recap->total_effective_working_days ?: 1;
        $pct = min(100, round(($recap->total_present / $effective) * 100));
        $pctColor = $pct >= 80
            ? 'var(--dash-status-success-text)'
            : ($pct >= 60 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-danger-text)');
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 text-center transition-all"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="text-3xl font-bold font-mono" style="color: var(--dash-status-success-text);">
                {{ $recap->total_present }}
            </div>
            <div class="text-xs font-semibold uppercase tracking-wider mt-1.5"
                 style="color: var(--dash-text-muted);">Hari Hadir</div>
        </div>

        <div class="rounded-2xl p-5 text-center transition-all"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="text-3xl font-bold font-mono" style="color: var(--dash-status-danger-text);">
                {{ $recap->total_absent }}
            </div>
            <div class="text-xs font-semibold uppercase tracking-wider mt-1.5"
                 style="color: var(--dash-text-muted);">Alpha</div>
        </div>

        <div class="rounded-2xl p-5 text-center transition-all"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="text-3xl font-bold font-mono" style="color: var(--dash-status-warning-text);">
                {{ $recap->total_late }}
            </div>
            <div class="text-xs font-semibold uppercase tracking-wider mt-1.5"
                 style="color: var(--dash-text-muted);">Terlambat</div>
            @if($recap->total_late_minutes > 0)
                <div class="text-xs font-mono mt-0.5" style="color: var(--dash-text-faint);">
                    {{ $recap->total_late_minutes }} menit
                </div>
            @endif
        </div>

        <div class="rounded-2xl p-5 text-center transition-all"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="text-3xl font-bold font-mono" style="color: {{ $pctColor }};">
                {{ $pct }}%
            </div>
            <div class="text-xs font-semibold uppercase tracking-wider mt-1.5"
                 style="color: var(--dash-text-muted);">Kehadiran</div>
        </div>
    </div>

    {{-- Secondary Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl px-5 py-4 flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                     style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
                    <span class="material-symbols-outlined text-[18px]">sick</span>
                </div>
                <span class="text-sm font-semibold" style="color: var(--dash-text-body);">Sakit</span>
            </div>
            <span class="text-lg font-bold font-mono" style="color: var(--dash-status-warning-text);">
                {{ $recap->total_sick }}
            </span>
        </div>

        <div class="rounded-2xl px-5 py-4 flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                     style="background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text);">
                    <span class="material-symbols-outlined text-[18px]">event_note</span>
                </div>
                <span class="text-sm font-semibold" style="color: var(--dash-text-body);">Izin</span>
            </div>
            <span class="text-lg font-bold font-mono" style="color: var(--dash-status-info-text);">
                {{ $recap->total_permission }}
            </span>
        </div>

        <div class="rounded-2xl px-5 py-4 flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                     style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                    <span class="material-symbols-outlined text-[18px]">beach_access</span>
                </div>
                <span class="text-sm font-semibold" style="color: var(--dash-text-body);">Cuti</span>
            </div>
            <span class="text-lg font-bold font-mono" style="color: var(--dash-primary-text);">
                {{ $recap->total_leave }}
            </span>
        </div>
    </div>

    {{-- Daily Log Table Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b flex items-center justify-between"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2.5">
                <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">
                    Log Harian Presensi
                </h2>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background-color: var(--dash-card-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                Total: {{ count($logs) }} Hari
            </span>
        </div>

        @if($logs->isEmpty())
            <div class="py-16 text-center px-4">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-2"
                      style="color: var(--dash-text-faint);">calendar_today</span>
                <p class="font-semibold text-sm" style="color: var(--dash-text-muted);">
                    Tidak ada log absensi untuk periode ini.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Tanggal</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Check-In</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Check-Out</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Terlambat</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Status</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            @php
                                $statusMap = [
                                    'present'    => ['Hadir',        'var(--dash-status-success-bg)', 'var(--dash-status-success-text)'],
                                    'checked_in' => ['Sedang Hadir', 'var(--dash-status-info-bg)',    'var(--dash-status-info-text)'],
                                    'absent'     => ['Alpha',        'var(--dash-status-danger-bg)',  'var(--dash-status-danger-text)'],
                                    'sick'       => ['Sakit',        'var(--dash-status-warning-bg)', 'var(--dash-status-warning-text)'],
                                    'permission' => ['Izin',         'var(--dash-status-info-bg)',    'var(--dash-status-info-text)'],
                                    'leave'      => ['Cuti',         'var(--dash-primary-bg)',        'var(--dash-primary-text)'],
                                    'holiday'    => ['Libur',        'var(--dash-status-default-bg)', 'var(--dash-status-default-text)'],
                                ];
                                [$label, $badgeBg, $badgeText] = $statusMap[$log->status] ?? [ucfirst($log->status), 'var(--dash-status-default-bg)', 'var(--dash-status-default-text)'];
                            @endphp
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                class="transition-colors"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-4 font-medium whitespace-nowrap" style="color: var(--dash-text-heading);">
                                    {{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('d M Y, l') }}
                                </td>
                                <td class="px-5 py-4 text-center font-mono text-sm whitespace-nowrap"
                                    style="color: var(--dash-text-body);">
                                    {{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i') : '-' }}
                                </td>
                                <td class="px-5 py-4 text-center font-mono text-sm whitespace-nowrap"
                                    style="color: var(--dash-text-body);">
                                    {{ $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('H:i') : '-' }}
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    @if($log->late_minutes > 0)
                                        <span class="font-semibold font-mono text-sm" style="color: var(--dash-status-warning-text);">
                                            {{ $log->late_minutes }}m
                                        </span>
                                    @else
                                        <span class="font-mono text-sm" style="color: var(--dash-text-faint);">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: {{ $badgeBg }}; color: {{ $badgeText }};">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    @if($log->is_location_anomaly)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold"
                                              style="color: var(--dash-status-danger-text);"
                                              title="{{ $log->anomaly_notes }}">
                                            <span class="material-symbols-outlined text-[14px]">warning</span>
                                            Anomali
                                        </span>
                                    @elseif($log->check_in_latitude)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold"
                                              style="color: var(--dash-status-success-text);">
                                            <span class="material-symbols-outlined text-[14px]">check</span>
                                            OK
                                        </span>
                                    @else
                                        <span class="font-mono text-xs" style="color: var(--dash-text-faint);">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
