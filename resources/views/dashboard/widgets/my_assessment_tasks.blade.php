@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-orange-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-orange-500 text-xl">assignment</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">My Assessment Tasks</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div class="text-center p-2 rounded-lg bg-orange-50">
                <p class="text-xl font-bold text-orange-600">{{ $data['pending_assessments'] ?? 0 }}</p>
                <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">Pending</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-emerald-50">
                <p class="text-xl font-bold text-emerald-600">{{ $data['completed_assessments'] ?? 0 }}</p>
                <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">Selesai</p>
            </div>
        </div>
        @forelse($data['recent_assessments'] ?? [] as $task)
            <div class="flex items-center justify-between p-2 rounded-lg bg-[var(--dash-nav-hover-bg)]">
                <span class="text-xs text-[color:var(--dash-text-body)] truncate max-w-[65%]">{{ $task['member'] ?? '-' }}</span>
                <span class="text-xs {{ $task['status'] === 'completed' ? 'text-emerald-600' : 'text-orange-500' }} font-semibold capitalize">{{ $task['status'] ?? '-' }}</span>
            </div>
        @empty
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center py-2">Tidak ada tugas</p>
        @endforelse
    </div>
</div>
