@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-xl">person_add</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Member Acquisition</h3>
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
        <div class="grid grid-cols-2 gap-3">
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Member Baru</p>
                <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $data['new_members'] ?? 0 }}</p>
            </div>
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Conversion Rate</p>
                <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-status-success-text);">{{ $data['conversion_rate'] ?? 0 }}%</p>
            </div>
        </div>

        @if(!empty($data['pipeline']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Pipeline</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($data['pipeline'] as $status => $count)
                        <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium" style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);">
                            {{ \App\Support\EnumLabels::get('payment_status', $status) }}: <strong style="color: var(--dash-text-heading);">{{ $count }}</strong>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
