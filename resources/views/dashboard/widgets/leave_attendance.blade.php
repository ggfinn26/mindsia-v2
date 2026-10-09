@props(['data'])

<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-amber-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-600 text-xl">event_busy</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Leave & Attendance</h3>
                <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Attendance Rate</p>
            <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">{{ $data['attendance_rate'] ?? 0 }}%</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Terlambat</p>
                <p class="text-sm font-semibold text-amber-600">{{ $data['total_late'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Sakit</p>
                <p class="text-sm font-semibold text-[color:var(--dash-text-body)]">{{ $data['total_sick'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Izin</p>
                <p class="text-sm font-semibold text-[color:var(--dash-text-body)]">{{ $data['total_permission'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Cuti</p>
                <p class="text-sm font-semibold text-blue-600">{{ $data['total_leave_days'] ?? 0 }}</p>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-[color:var(--dash-text-faint)]">Cuti Pending</span>
            <span class="text-sm font-semibold text-amber-600">{{ $data['pending_leaves'] ?? 0 }}</span>
        </div>

        @if(!empty($data['by_leave_type']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-2">Tipe Cuti</p>
            @foreach($data['by_leave_type'] as $type => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-[color:var(--dash-text-body)]">{{ ucfirst($type) }}</span>
                    <span class="text-xs font-semibold text-[color:var(--dash-text-body)]">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
