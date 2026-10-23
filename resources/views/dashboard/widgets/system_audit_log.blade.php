@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">manage_search</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">System Audit Log</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">Aktivitas terkini</p>
        </div>
    </div>
    <div class="space-y-2 flex-grow">
        @forelse($data['recent_logs'] ?? [] as $log)
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold truncate" style="color: var(--dash-text-heading);">{{ $log['description'] ?? '-' }}</p>
                        <p class="text-[11px]" style="color: var(--dash-text-muted);">{{ $log['causer'] ?? 'System' }} · {{ $log['subject_type'] ?? '-' }}</p>
                    </div>
                    <span class="text-[11px] whitespace-nowrap shrink-0" style="color: var(--dash-text-muted);">{{ \Carbon\Carbon::parse($log['created_at'])->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <div class="py-4 text-center">
                <p class="text-xs" style="color: var(--dash-text-muted);">Tidak ada log</p>
            </div>
        @endforelse
        @if(isset($data['total_today']))
            <p class="text-xs text-center font-medium pt-1" style="color: var(--dash-text-muted);">{{ $data['total_today'] }} aktivitas hari ini</p>
        @endif
    </div>
</div>
