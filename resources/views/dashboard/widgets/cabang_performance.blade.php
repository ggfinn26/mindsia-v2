@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-xl">location_city</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Cabang Performance</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['trend_percent']))
            <div class="flex items-center gap-1 text-xs font-semibold" style="color: {{ $data['trend_percent'] >= 0 ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};">
                <span class="material-symbols-outlined text-sm">{{ $data['trend_percent'] >= 0 ? 'trending_up' : 'trending_down' }}</span>
                <span>{{ abs($data['trend_percent']) }}%</span>
            </div>
        @endif
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Revenue</p>
            <p class="text-2xl sm:text-3xl font-bold font-jakarta tracking-tight" style="color: var(--dash-text-heading);">Rp {{ number_format($data['revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Karyawan Aktif</p>
                <p class="text-base font-bold mt-0.5" style="color: var(--dash-text-heading);">{{ $data['active_employees'] ?? 0 }}</p>
            </div>
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Kelas Aktif</p>
                <p class="text-base font-bold mt-0.5" style="color: var(--dash-text-heading);">{{ $data['active_classes'] ?? 0 }}</p>
            </div>
        </div>

        @if(isset($data['operational_cost']) && $data['operational_cost'] > 0)
            <div class="flex items-center justify-between pt-2 border-t" style="border-color: var(--dash-card-border);">
                <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Biaya Operasional</span>
                <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">Rp {{ number_format($data['operational_cost'], 0, ',', '.') }}</span>
            </div>
        @endif
    </div>
</div>
