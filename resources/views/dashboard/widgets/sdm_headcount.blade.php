@props(['data'])

<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-violet-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-violet-600 text-xl">groups</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">SDM Headcount</h3>
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
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Karyawan Aktif</p>
            <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">{{ $data['active_employees'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Headcount Target</p>
                <p class="text-sm font-semibold text-[color:var(--dash-text-body)]">{{ $data['requested_headcount'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Gap</p>
                <p class="text-sm font-semibold {{ ($data['headcount_gap'] ?? 0) > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $data['headcount_gap'] ?? 0 }}</p>
            </div>
        </div>

        @if(!empty($data['by_employment_type']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-2">Tipe Karyawan</p>
            @foreach($data['by_employment_type'] as $type => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-[color:var(--dash-text-body)]">{{ strtoupper($type) }}</span>
                    <span class="text-xs font-semibold text-[color:var(--dash-text-body)]">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
