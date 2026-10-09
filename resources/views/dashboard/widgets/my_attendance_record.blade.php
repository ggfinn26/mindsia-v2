@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-[var(--dash-nav-hover-bg)] flex items-center justify-center">
            <span class="material-symbols-outlined text-[color:var(--dash-text-body)] text-xl">fingerprint</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">My Attendance Record</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-4 gap-1.5">
            <div class="text-center p-2 rounded-lg bg-emerald-50">
                <p class="text-lg font-bold text-emerald-600">{{ $data['hadir'] ?? 0 }}</p>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Hadir</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-yellow-50">
                <p class="text-lg font-bold text-yellow-600">{{ $data['terlambat'] ?? 0 }}</p>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Lambat</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-blue-50">
                <p class="text-lg font-bold text-blue-600">{{ $data['izin'] ?? 0 }}</p>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Izin</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-red-50">
                <p class="text-lg font-bold text-red-500">{{ $data['alpha'] ?? 0 }}</p>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Alpha</p>
            </div>
        </div>
        <div class="pt-2 border-t border-gray-50 flex justify-between items-center">
            <span class="text-xs text-[color:var(--dash-text-faint)]">Attendance Rate</span>
            <span class="text-sm font-bold {{ ($data['attendance_rate'] ?? 0) >= 90 ? 'text-emerald-600' : (($data['attendance_rate'] ?? 0) >= 75 ? 'text-yellow-600' : 'text-red-500') }}">
                {{ $data['attendance_rate'] ?? 0 }}%
            </span>
        </div>
    </div>
</div>
