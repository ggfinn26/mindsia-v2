@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-[var(--dash-nav-hover-bg)] flex items-center justify-center">
            <span class="material-symbols-outlined text-[color:var(--dash-text-body)] text-xl">manage_search</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">System Audit Log</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">Aktivitas terkini</p>
        </div>
    </div>
    <div class="space-y-2">
        @forelse($data['recent_logs'] ?? [] as $log)
            <div class="p-2 rounded-lg bg-[var(--dash-nav-hover-bg)]">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-[color:var(--dash-text-heading)] truncate">{{ $log['description'] ?? '-' }}</p>
                        <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $log['causer'] ?? 'System' }} · {{ $log['subject_type'] ?? '-' }}</p>
                    </div>
                    <span class="text-xs text-gray-300 whitespace-nowrap">{{ \Carbon\Carbon::parse($log['created_at'])->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center py-4">Tidak ada log</p>
        @endforelse
        @if(isset($data['total_today']))
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center pt-1">{{ $data['total_today'] }} aktivitas hari ini</p>
        @endif
    </div>
</div>
