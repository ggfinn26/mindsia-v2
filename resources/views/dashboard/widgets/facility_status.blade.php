@props(['data'])

<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg {{ ($data['high_priority'] ?? 0) > 0 ? 'bg-red-50' : 'bg-orange-50' }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ ($data['high_priority'] ?? 0) > 0 ? 'text-red-500' : 'text-orange-600' }} text-xl">build</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Facility Status</h3>
                <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(($data['high_priority'] ?? 0) > 0)
            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">
                {{ $data['high_priority'] }} high
            </span>
        @endif
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Tiket Terbuka</p>
            <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">{{ $data['open_tickets'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Diselesaikan Bulan Ini</p>
                <p class="text-sm font-semibold text-emerald-600">{{ $data['resolved_this_month'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Total Biaya</p>
                <p class="text-sm font-semibold text-[color:var(--dash-text-body)]">Rp {{ number_format($data['total_cost'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        @if(!empty($data['by_status']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-2">By Status</p>
            <div class="space-y-1">
                @foreach($data['by_status'] as $status => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-[color:var(--dash-text-muted)]">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                        <span class="text-xs font-medium text-[color:var(--dash-text-body)]">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
