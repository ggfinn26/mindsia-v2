@props(['data'])

<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-xl">location_city</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Cabang Performance</h3>
                <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['trend_percent']))
            <div class="flex items-center gap-1 {{ $data['trend_percent'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                <span class="material-symbols-outlined text-sm">{{ $data['trend_percent'] >= 0 ? 'trending_up' : 'trending_down' }}</span>
                <span class="text-xs font-semibold">{{ abs($data['trend_percent']) }}%</span>
            </div>
        @endif
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Revenue</p>
            <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">Rp {{ number_format($data['revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Karyawan Aktif</p>
                <p class="text-sm font-semibold text-[color:var(--dash-text-body)]">{{ $data['active_employees'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Kelas Aktif</p>
                <p class="text-sm font-semibold text-[color:var(--dash-text-body)]">{{ $data['active_classes'] ?? 0 }}</p>
            </div>
        </div>

        @if(isset($data['operational_cost']) && $data['operational_cost'] > 0)
        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-[color:var(--dash-text-faint)]">Biaya Operasional</span>
            <span class="text-sm font-semibold text-[color:var(--dash-text-body)]">Rp {{ number_format($data['operational_cost'], 0, ',', '.') }}</span>
        </div>
        @endif
    </div>
</div>
