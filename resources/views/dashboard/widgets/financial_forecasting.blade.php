@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-sky-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-sky-600 text-xl">query_stats</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Financial Forecasting</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">Proyeksi: {{ $data['next_period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-3 gap-2">
            <div class="text-center p-2 rounded-lg bg-emerald-50">
                <p class="text-xs font-bold text-emerald-600">Rp {{ number_format(($data['forecast_revenue'] ?? 0) / 1000000, 1) }} Juta</p>
                <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">Revenue</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-red-50">
                <p class="text-xs font-bold text-red-500">Rp {{ number_format(($data['forecast_cost'] ?? 0) / 1000000, 1) }} Juta</p>
                <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">Cost</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-blue-50">
                <p class="text-xs font-bold {{ ($data['forecast_margin'] ?? 0) >= 0 ? 'text-blue-600' : 'text-red-500' }}">
                    Rp {{ number_format(($data['forecast_margin'] ?? 0) / 1000000, 1) }} Juta
                </p>
                <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">Margin</p>
            </div>
        </div>
        <p class="text-xs text-[color:var(--dash-text-faint)] text-center">Berdasarkan rata-rata 3 bulan terakhir</p>
    </div>
</div>
