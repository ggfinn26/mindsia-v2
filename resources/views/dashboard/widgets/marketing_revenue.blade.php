@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-xl">campaign</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Marketing Revenue</h3>
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
            <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Total Revenue</p>
            <p class="text-2xl sm:text-3xl font-bold font-jakarta tracking-tight" style="color: var(--dash-text-heading);">Rp {{ number_format($data['total_revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>

        @if(!empty($data['by_employee']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Top Marketer</p>
                <div class="space-y-1.5">
                    @foreach(array_slice($data['by_employee'], 0, 3) as $emp)
                        <div class="flex items-center justify-between text-xs">
                            <span class="truncate max-w-[60%]" style="color: var(--dash-text-body);">{{ $emp['employee'] }}</span>
                            <span class="font-semibold" style="color: var(--dash-text-heading);">Rp {{ number_format($emp['amount'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
