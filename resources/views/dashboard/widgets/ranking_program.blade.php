@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-violet-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-violet-600 text-xl">workspace_premium</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Ranking Program</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-2">
        @forelse($data['programs'] ?? [] as $i => $program)
            <div class="flex items-center gap-2 p-2 rounded-lg {{ $i === 0 ? 'bg-violet-50 ring-1 ring-violet-200' : 'bg-[var(--dash-nav-hover-bg)]' }}">
                <span class="text-xs font-bold {{ $i === 0 ? 'text-violet-400' : 'text-gray-300' }} w-4">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-[color:var(--dash-text-heading)] truncate">{{ $program['program'] }}</p>
                    <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $program['member_count'] }} member</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-[color:var(--dash-text-heading)]">Rp {{ number_format($program['revenue'] / 1000000, 1) }} Juta</p>
                </div>
            </div>
        @empty
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center py-4">Tidak ada data</p>
        @endforelse
    </div>
</div>
