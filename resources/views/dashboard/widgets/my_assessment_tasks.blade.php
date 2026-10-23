@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
            <span class="material-symbols-outlined text-xl">assignment</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">My Assessment Tasks</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="text-center p-2.5 rounded-lg" style="background-color: var(--dash-status-warning-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-status-warning-text);">{{ $data['pending_assessments'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Pending</p>
            </div>
            <div class="text-center p-2.5 rounded-lg" style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-status-success-text);">{{ $data['completed_assessments'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Selesai</p>
            </div>
        </div>
        <div class="space-y-1.5 flex-grow">
            @forelse($data['recent_assessments'] ?? [] as $task)
                <div class="flex items-center justify-between p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <span class="text-xs truncate max-w-[65%]" style="color: var(--dash-text-body);">{{ $task['member'] ?? '-' }}</span>
                    <span class="text-xs font-semibold capitalize" style="color: {{ ($task['status'] ?? '') === 'completed' ? 'var(--dash-status-success-text)' : 'var(--dash-status-warning-text)' }};">{{ $task['status'] ?? '-' }}</span>
                </div>
            @empty
                <div class="py-4 text-center">
                    <p class="text-xs" style="color: var(--dash-text-muted);">Tidak ada tugas</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
