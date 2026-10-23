@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">balance</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Budget vs Actual</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Budget</p>
                <p class="text-base sm:text-lg font-bold font-jakarta" style="color: var(--dash-text-heading);">Rp {{ number_format($data['budget'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Actual</p>
                <p class="text-base sm:text-lg font-bold font-jakarta" style="color: var(--dash-text-heading);">Rp {{ number_format($data['actual'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Variance</p>
                <p class="text-sm font-bold" style="color: {{ ($data['variance'] ?? 0) >= 0 ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};">
                    {{ ($data['variance'] ?? 0) >= 0 ? '+' : '' }}Rp {{ number_format($data['variance'] ?? 0, 0, ',', '.') }}
                    <span class="text-xs font-semibold">({{ ($data['variance'] ?? 0) >= 0 ? '+' : '' }}{{ $data['variance_percent'] ?? 0 }}%)</span>
                </p>
            </div>
        </div>
    </div>
</div>
