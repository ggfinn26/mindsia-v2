@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">summarize</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Financial Reports</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">YTD {{ $data['year'] ?? date('Y') }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-3 gap-2">
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-bold" style="color: var(--dash-status-success-text);">Rp {{ number_format(($data['ytd_revenue'] ?? 0) / 1000000, 1) }} Juta</p>
                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Revenue</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-bold" style="color: var(--dash-status-danger-text);">Rp {{ number_format(($data['ytd_cost'] ?? 0) / 1000000, 1) }} Juta</p>
                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Cost</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-bold" style="color: {{ ($data['ytd_margin'] ?? 0) >= 0 ? 'var(--dash-primary-text)' : 'var(--dash-status-danger-text)' }};">Rp {{ number_format(($data['ytd_margin'] ?? 0) / 1000000, 1) }} Juta</p>
                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Margin</p>
            </div>
        </div>
        @if(!empty($data['monthly_trend']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Tren Bulanan (Rp)</p>
                <div class="space-y-1">
                    @foreach(array_slice($data['monthly_trend'], -3) as $month)
                        <div class="flex justify-between items-center text-xs">
                            <span style="color: var(--dash-text-body);">{{ $month['month'] }}</span>
                            <span class="font-semibold" style="color: var(--dash-text-heading);">Rp {{ number_format($month['revenue'] / 1000000, 1) }} Juta</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
