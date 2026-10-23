@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">history</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">My Performance History</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">6 Bulan Terakhir</p>
        </div>
    </div>
    <div class="space-y-2 flex-grow">
        @forelse($data['history'] ?? [] as $record)
            <div class="flex items-center justify-between p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <span class="text-xs font-medium" style="color: var(--dash-text-body);">{{ $record['period'] }}</span>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold" style="color: var(--dash-text-heading);">{{ $record['score'] ?? '-' }}</span>
                    @if(isset($record['grade']))
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">{{ $record['grade'] }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-4 text-center">
                <p class="text-xs" style="color: var(--dash-text-muted);">Belum ada histori</p>
            </div>
        @endforelse
    </div>
</div>
