@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">fingerprint</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">My Attendance Record</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-4 gap-1.5">
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-base font-bold font-jakarta" style="color: var(--dash-status-success-text);">{{ $data['hadir'] ?? 0 }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Hadir</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-base font-bold font-jakarta" style="color: var(--dash-status-warning-text);">{{ $data['terlambat'] ?? 0 }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Lambat</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-base font-bold font-jakarta" style="color: var(--dash-primary-text);">{{ $data['izin'] ?? 0 }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Izin</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-base font-bold font-jakarta" style="color: var(--dash-status-danger-text);">{{ $data['alpha'] ?? 0 }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Alpha</p>
            </div>
        </div>
        <div class="pt-2 border-t flex justify-between items-center" style="border-color: var(--dash-card-border);">
            <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Attendance Rate</span>
            <span class="text-sm font-bold" style="color: {{ ($data['attendance_rate'] ?? 0) >= 90 ? 'var(--dash-status-success-text)' : (($data['attendance_rate'] ?? 0) >= 75 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-danger-text)') }};">
                {{ $data['attendance_rate'] ?? 0 }}%
            </span>
        </div>
    </div>
</div>
