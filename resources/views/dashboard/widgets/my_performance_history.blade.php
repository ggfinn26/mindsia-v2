@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-indigo-600 text-xl">history</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">My Performance History</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">6 Bulan Terakhir</p>
        </div>
    </div>
    <div class="space-y-2">
        @forelse($data['history'] ?? [] as $record)
            <div class="flex items-center justify-between p-2 rounded-lg bg-[var(--dash-nav-hover-bg)]">
                <span class="text-xs text-[color:var(--dash-text-body)]">{{ $record['period'] }}</span>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-[color:var(--dash-text-heading)]">{{ $record['score'] ?? '-' }}</span>
                    @if(isset($record['grade']))
                        <span class="text-xs px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 font-bold">{{ $record['grade'] }}</span>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center py-4">Belum ada histori</p>
        @endforelse
    </div>
</div>
