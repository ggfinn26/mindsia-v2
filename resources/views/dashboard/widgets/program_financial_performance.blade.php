@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">school</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Program Financial Performance</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-2 flex-grow">
        @forelse($data['programs'] ?? [] as $program)
            <div class="flex items-center justify-between p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <span class="text-xs font-medium truncate max-w-[55%]" style="color: var(--dash-text-body);">{{ $program['program'] }}</span>
                <div class="text-right shrink-0">
                    <p class="text-xs font-bold" style="color: var(--dash-text-heading);">Rp {{ number_format($program['revenue'], 0, ',', '.') }}</p>
                    <p class="text-[11px]" style="color: var(--dash-text-muted);">{{ $program['member_count'] }} member</p>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-6 text-center">
                <span class="material-symbols-outlined text-2xl mb-1" style="color: var(--dash-text-muted);">info</span>
                <p class="text-xs" style="color: var(--dash-text-muted);">Tidak ada data</p>
            </div>
        @endforelse
    </div>
</div>
