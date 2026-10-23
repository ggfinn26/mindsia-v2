@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: {{ ($data['high_priority'] ?? 0) > 0 ? 'var(--dash-status-danger-bg)' : 'var(--dash-status-warning-bg)' }}; color: {{ ($data['high_priority'] ?? 0) > 0 ? 'var(--dash-status-danger-text)' : 'var(--dash-status-warning-text)' }};">
                <span class="material-symbols-outlined text-xl">build</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Facility Status</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(($data['high_priority'] ?? 0) > 0)
            <span class="px-2.5 py-1 text-xs font-bold rounded-full" style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text);">
                {{ $data['high_priority'] }} high
            </span>
        @endif
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Tiket Terbuka</p>
            <p class="text-2xl sm:text-3xl font-bold font-jakarta tracking-tight" style="color: var(--dash-text-heading);">{{ $data['open_tickets'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Selesai Bulan Ini</p>
                <p class="text-base font-bold mt-0.5" style="color: var(--dash-status-success-text);">{{ $data['resolved_this_month'] ?? 0 }}</p>
            </div>
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Total Biaya</p>
                <p class="text-base font-bold mt-0.5" style="color: var(--dash-text-heading);">Rp {{ number_format($data['total_cost'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        @if(!empty($data['by_status']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">By Status</p>
                <div class="space-y-1">
                    @foreach($data['by_status'] as $status => $count)
                        <div class="flex items-center justify-between text-xs">
                            <span style="color: var(--dash-text-body);">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                            <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
