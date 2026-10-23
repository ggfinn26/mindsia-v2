@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">map</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Area Performance</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        @forelse($data['areas'] ?? [] as $area)
            <div class="p-3 rounded-lg transition-colors" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-sm font-semibold truncate" style="color: var(--dash-text-heading);">{{ $area['area'] }}</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">Rp {{ number_format($area['revenue'] / 1000000, 1) }} Juta</span>
                </div>
                <div class="flex items-center gap-3 text-xs" style="color: var(--dash-text-muted);">
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">store</span>
                        <span>{{ $area['branch_count'] }} cabang</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">person_add</span>
                        <span>{{ $area['new_members'] }} baru</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-6 text-center">
                <span class="material-symbols-outlined text-2xl mb-1" style="color: var(--dash-text-muted);">info</span>
                <p class="text-xs" style="color: var(--dash-text-muted);">Tidak ada data</p>
            </div>
        @endforelse
    </div>
</div>
