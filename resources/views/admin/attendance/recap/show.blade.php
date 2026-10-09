@extends('layouts.dashboard')

@section('title', 'Detail Rekap Absensi')

@push('head-scripts')
<style>
@media print {
    aside, header, .no-print { display: none !important; }
    main { padding: 0 !important; overflow: visible !important; }
}
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex flex-wrap justify-between items-start gap-4 mb-6 no-print">
        <div>
            <h1 class="text-2xl font-bold text-[var(--dash-text-heading)]">
                Detail Rekap — {{ $recap->employee->full_name }}
            </h1>
            <p class="text-sm text-[var(--dash-text-muted)] mt-1">
                {{ \Carbon\Carbon::create($recap->period_year, $recap->period_month)->translatedFormat('F Y') }}
                &nbsp;·&nbsp; {{ $recap->employee->branch?->branch_name ?? '-' }}
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 border border-[var(--dash-input-border)] rounded-lg text-sm font-medium text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] hover:bg-[var(--dash-page-bg)]">
                <span class="material-symbols-outlined text-base">print</span> Cetak / PDF
            </button>
            <a href="{{ route('attendance-recaps.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-[var(--dash-input-border)] rounded-lg text-sm font-medium text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] hover:bg-[var(--dash-page-bg)]">
                Kembali
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @php
            $effective = $recap->total_effective_working_days ?: 1;
            $pct = min(100, round(($recap->total_present / $effective) * 100));
        @endphp
        <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-emerald-600">{{ $recap->total_present }}</div>
            <div class="text-xs text-[var(--dash-text-muted)] mt-1">Hari Hadir</div>
        </div>
        <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-rose-600">{{ $recap->total_absent }}</div>
            <div class="text-xs text-[var(--dash-text-muted)] mt-1">Alpha</div>
        </div>
        <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-amber-600">{{ $recap->total_late }}</div>
            <div class="text-xs text-[var(--dash-text-muted)] mt-1">Terlambat</div>
            @if($recap->total_late_minutes > 0)
                <div class="text-xs text-[var(--dash-text-faint)]">{{ $recap->total_late_minutes }} menit</div>
            @endif
        </div>
        <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg p-4 text-center">
            <div class="text-3xl font-bold {{ $pct >= 80 ? 'text-emerald-600' : ($pct >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                {{ $pct }}%
            </div>
            <div class="text-xs text-[var(--dash-text-muted)] mt-1">Kehadiran</div>
        </div>
    </div>

    {{-- Secondary stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg px-4 py-3 flex justify-between items-center">
            <span class="text-sm text-[var(--dash-text-body)]">Sakit</span>
            <span class="font-semibold text-amber-700">{{ $recap->total_sick }}</span>
        </div>
        <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg px-4 py-3 flex justify-between items-center">
            <span class="text-sm text-[var(--dash-text-body)]">Izin</span>
            <span class="font-semibold text-blue-700">{{ $recap->total_permission }}</span>
        </div>
        <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg px-4 py-3 flex justify-between items-center">
            <span class="text-sm text-[var(--dash-text-body)]">Cuti</span>
            <span class="font-semibold text-indigo-700">{{ $recap->total_leave }}</span>
        </div>
    </div>

    {{-- Daily log table --}}
    <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-[var(--dash-card-border)]">
            <h2 class="text-sm font-semibold text-[var(--dash-text-body)]">Log Harian</h2>
        </div>
        @if($logs->isEmpty())
            <div class="py-10 text-center text-sm text-[var(--dash-text-faint)]">Tidak ada log absensi untuk periode ini.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                    <thead class="bg-[var(--dash-page-bg)]">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase">Tanggal</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase">Check-In</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase">Check-Out</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase">Terlambat</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase">Status</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-[var(--dash-text-muted)] uppercase">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--dash-card-border)]">
                        @foreach($logs as $log)
                            <tr class="hover:bg-[var(--dash-page-bg)]">
                                <td class="px-5 py-3 font-medium text-[var(--dash-text-heading)]">
                                    {{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('d M, l') }}
                                </td>
                                <td class="px-5 py-3 text-center text-[var(--dash-text-body)]">
                                    {{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i') : '-' }}
                                </td>
                                <td class="px-5 py-3 text-center text-[var(--dash-text-body)]">
                                    {{ $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('H:i') : '-' }}
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($log->late_minutes > 0)
                                        <span class="text-amber-600 font-medium">{{ $log->late_minutes }}m</span>
                                    @else
                                        <span class="text-[var(--dash-text-faint)]">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $statusMap = [
                                            'present'    => ['Hadir',          'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                            'checked_in' => ['Sedang Hadir',   'bg-indigo-50 text-indigo-700 border-indigo-200'],
                                            'absent'     => ['Alpha',          'bg-rose-50 text-rose-700 border-rose-200'],
                                            'sick'       => ['Sakit',          'bg-amber-50 text-amber-700 border-amber-200'],
                                            'permission' => ['Izin',           'bg-blue-50 text-blue-700 border-blue-200'],
                                            'leave'      => ['Cuti',           'bg-purple-50 text-purple-700 border-purple-200'],
                                            'holiday'    => ['Libur',          'bg-teal-50 text-teal-700 border-teal-200'],
                                        ];
                                        [$label, $cls] = $statusMap[$log->status] ?? [ucfirst($log->status), 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)] border-[var(--dash-card-border)]'];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $cls }}">{{ $label }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($log->is_location_anomaly)
                                        <span class="text-xs text-rose-500" title="{{ $log->anomaly_notes }}">⚠ Anomali</span>
                                    @elseif($log->check_in_latitude)
                                        <span class="text-xs text-[var(--dash-text-faint)]">OK</span>
                                    @else
                                        <span class="text-[var(--dash-text-faint)]">-</span>
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
