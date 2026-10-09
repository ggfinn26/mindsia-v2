@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-emerald-600 [.mode-dark_&]:text-emerald-400 text-xl">fact_check</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[var(--dash-text-heading)]">My Session Attendance</h3>
            <p class="text-xs text-[var(--dash-text-muted)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-3 gap-2">
            <div class="text-center p-2 rounded-lg bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10">
                <p class="text-xl font-bold text-emerald-600 [.mode-dark_&]:text-emerald-400">{{ $data['hadir'] ?? 0 }}</p>
                <p class="text-xs text-[var(--dash-text-muted)] mt-0.5">Hadir</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-amber-50 [.mode-dark_&]:bg-amber-500/10">
                <p class="text-xl font-bold text-amber-600 [.mode-dark_&]:text-amber-400">{{ $data['izin'] ?? 0 }}</p>
                <p class="text-xs text-[var(--dash-text-muted)] mt-0.5">Izin</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-rose-50 [.mode-dark_&]:bg-rose-500/10">
                <p class="text-xl font-bold text-rose-500 [.mode-dark_&]:text-rose-400">{{ $data['tidak_hadir'] ?? 0 }}</p>
                <p class="text-xs text-[var(--dash-text-muted)] mt-0.5">Absen</p>
            </div>
        </div>
        <div class="pt-2 border-t border-[var(--dash-card-border)] flex justify-between items-center">
            <span class="text-xs text-[var(--dash-text-muted)]">Attendance Rate</span>
            <span class="text-sm font-bold {{ ($data['attendance_rate'] ?? 0) >= 80 ? 'text-emerald-600 [.mode-dark_&]:text-emerald-400' : 'text-rose-500 [.mode-dark_&]:text-rose-400' }}">{{ $data['attendance_rate'] ?? 0 }}%</span>
        </div>
    </div>
</div>
