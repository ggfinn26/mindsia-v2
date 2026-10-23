@props(['data'])

@php
    $rate = $data['attendance_rate'] ?? 0;
    $barBg = $rate >= 95 ? 'var(--dash-status-success-text)' : ($rate >= 80 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-danger-text)');
@endphp

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-xl">schedule</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Attendance Summary</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['trend_percent']))
            <div class="flex items-center gap-1 text-xs font-semibold" style="color: {{ $data['trend_percent'] >= 0 ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};">
                <span class="material-symbols-outlined text-sm">{{ $data['trend_percent'] >= 0 ? 'trending_up' : 'trending_down' }}</span>
                <span>{{ abs($data['trend_percent']) }}%</span>
            </div>
        @endif
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Kehadiran</span>
                <span class="text-xs font-bold" style="color: {{ $rate >= 95 ? 'var(--dash-status-success-text)' : ($rate >= 80 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-danger-text)') }};">{{ $rate }}%</span>
            </div>
            <div class="w-full rounded-full h-2 overflow-hidden" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <div class="h-2 rounded-full transition-all" style="width: {{ min($rate, 100) }}%; background-color: {{ $barBg }};"></div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Hadir</p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--dash-status-success-text);">{{ $data['total_present'] ?? 0 }}</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Absen</p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--dash-status-danger-text);">{{ $data['total_absent'] ?? 0 }}</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Terlambat</p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--dash-status-warning-text);">{{ $data['total_late'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="text-center">
                <p class="text-xs" style="color: var(--dash-text-muted);">Sakit</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $data['total_sick'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs" style="color: var(--dash-text-muted);">Izin</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $data['total_permission'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs" style="color: var(--dash-text-muted);">Cuti</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $data['total_leave'] ?? 0 }}</p>
            </div>
        </div>

        @if(isset($data['anomaly_count']) && $data['anomaly_count'] > 0)
            <div class="flex items-center justify-between pt-2 border-t" style="border-color: var(--dash-card-border);">
                <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Anomali Lokasi</span>
                <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text);">{{ $data['anomaly_count'] }}</span>
            </div>
        @endif
    </div>
</div>
