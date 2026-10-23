@props(['data'])

@php
    $utilization = $data['utilization_percent'] ?? 0;
    $barBg = $utilization >= 80 ? 'var(--dash-status-success-text)' : ($utilization >= 50 ? 'var(--dash-primary)' : 'var(--dash-status-warning-text)');
@endphp

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-xl">school</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Class Schedule & Utilization</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Kelas Aktif</p>
            <p class="text-2xl sm:text-3xl font-bold font-jakarta tracking-tight" style="color: var(--dash-text-heading);">{{ $data['active_classes'] ?? 0 }}</p>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Utilisasi Kuota</span>
                <span class="text-xs font-bold" style="color: {{ $barBg }};">{{ $utilization }}%</span>
            </div>
            <div class="w-full rounded-full h-2 overflow-hidden" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <div class="h-2 rounded-full transition-all" style="width: {{ min($utilization, 100) }}%; background-color: {{ $barBg }};"></div>
            </div>
            <div class="flex items-center justify-between mt-1 text-xs" style="color: var(--dash-text-muted);">
                <span>{{ $data['active_members'] ?? 0 }} siswa</span>
                <span>dari {{ $data['total_quota'] ?? 0 }} kuota</span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Planned</p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--dash-text-heading);">{{ $data['planned_classes'] ?? 0 }}</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Aktif</p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--dash-status-success-text);">{{ $data['active_classes'] ?? 0 }}</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Selesai</p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--dash-primary-text);">{{ $data['completed_classes'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
