@props(['data'])

@php
    $utilization = $data['utilization_percent'] ?? 0;
    $barColor = $utilization >= 80 ? 'bg-emerald-500' : ($utilization >= 50 ? 'bg-blue-500' : 'bg-amber-500');
@endphp

<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-indigo-600 text-xl">school</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Class Schedule & Utilization</h3>
                <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Kelas Aktif</p>
            <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">{{ $data['active_classes'] ?? 0 }}</p>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-[color:var(--dash-text-faint)]">Utilisasi Kuota</span>
                <span class="text-xs font-semibold text-[color:var(--dash-text-body)]">{{ $utilization }}%</span>
            </div>
            <div class="w-full bg-[var(--dash-nav-hover-bg)] rounded-full h-2">
                <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ min($utilization, 100) }}%"></div>
            </div>
            <div class="flex items-center justify-between mt-1">
                <span class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['active_members'] ?? 0 }} siswa</span>
                <span class="text-xs text-[color:var(--dash-text-faint)]">dari {{ $data['total_quota'] ?? 0 }} kuota</span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-50">
            <div class="text-center">
                <p class="text-xs text-[color:var(--dash-text-faint)]">Planned</p>
                <p class="text-sm font-semibold text-[color:var(--dash-text-body)]">{{ $data['planned_classes'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-[color:var(--dash-text-faint)]">Aktif</p>
                <p class="text-sm font-semibold text-emerald-600">{{ $data['active_classes'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-[color:var(--dash-text-faint)]">Selesai</p>
                <p class="text-sm font-semibold text-blue-600">{{ $data['completed_classes'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
