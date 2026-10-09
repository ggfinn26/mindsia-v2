@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-violet-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-violet-600 text-xl">school</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Program Financial Performance</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-2">
        @forelse($data['programs'] ?? [] as $program)
            <div class="flex items-center justify-between p-2 rounded-lg bg-[var(--dash-nav-hover-bg)]">
                <span class="text-xs font-medium text-[color:var(--dash-text-body)] truncate max-w-[55%]">{{ $program['program'] }}</span>
                <div class="text-right">
                    <p class="text-xs font-bold text-[color:var(--dash-text-heading)]">Rp {{ number_format($program['revenue'], 0, ',', '.') }}</p>
                    <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $program['member_count'] }} member</p>
                </div>
            </div>
        @empty
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center py-4">Tidak ada data</p>
        @endforelse
    </div>
</div>
