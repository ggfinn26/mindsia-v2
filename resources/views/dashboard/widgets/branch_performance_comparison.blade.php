@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-purple-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-purple-600 text-xl">compare</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Branch Performance Comparison</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-2">
        @forelse($data['branches'] ?? [] as $i => $branch)
            <div class="flex items-center gap-2 p-2 rounded-lg {{ $i === 0 ? 'bg-purple-50 ring-1 ring-purple-200' : 'bg-[var(--dash-nav-hover-bg)]' }}">
                <span class="text-xs font-bold text-[color:var(--dash-text-faint)] w-4">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-[color:var(--dash-text-heading)] truncate">{{ $branch['branch'] }}</p>
                    <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $branch['member_count'] }} member • {{ $branch['employee_count'] }} SDM</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-[color:var(--dash-text-heading)]">Rp {{ number_format($branch['revenue'] / 1000000, 1) }} Juta</p>
                    <p class="text-xs {{ ($branch['margin_percent'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $branch['margin_percent'] ?? 0 }}%</p>
                </div>
            </div>
        @empty
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center py-4">Tidak ada data</p>
        @endforelse
    </div>
</div>
