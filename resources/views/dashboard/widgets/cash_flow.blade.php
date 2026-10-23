@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text);">
            <span class="material-symbols-outlined text-xl">waterfall_chart</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Cash Flow</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Inflow</p>
                <p class="text-base sm:text-lg font-bold font-jakarta" style="color: var(--dash-status-success-text);">Rp {{ number_format($data['inflow'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Outflow</p>
                <p class="text-base sm:text-lg font-bold font-jakarta" style="color: var(--dash-status-danger-text);">Rp {{ number_format($data['total_outflow'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Net Cash Flow</p>
                <p class="text-sm font-bold" style="color: {{ ($data['net_cash_flow'] ?? 0) >= 0 ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};">
                    {{ ($data['net_cash_flow'] ?? 0) >= 0 ? '+' : '' }}Rp {{ number_format($data['net_cash_flow'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="pt-1 space-y-1.5">
            <div class="flex justify-between text-xs" style="color: var(--dash-text-muted);">
                <span>Payroll</span>
                <span class="font-medium" style="color: var(--dash-text-heading);">Rp {{ number_format($data['payroll_outflow'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs" style="color: var(--dash-text-muted);">
                <span>Opex</span>
                <span class="font-medium" style="color: var(--dash-text-heading);">Rp {{ number_format($data['opex_outflow'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
