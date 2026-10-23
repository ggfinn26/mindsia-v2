@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-xl">storefront</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Branch Performance</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['branch'] ?? '-' }} · {{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Revenue</p>
                <p class="text-base sm:text-lg font-bold font-jakarta" style="color: var(--dash-text-heading);">Rp {{ number_format($data['revenue'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Margin</p>
                <p class="text-base sm:text-lg font-bold font-jakarta" style="color: {{ ($data['margin'] ?? 0) >= 0 ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};">
                    {{ $data['margin_percent'] ?? 0 }}%
                </p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-2 pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-sm font-bold" style="color: var(--dash-text-heading);">{{ $data['active_members'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Member Aktif</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-sm font-bold" style="color: var(--dash-text-heading);">{{ $data['active_classes'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Kelas Aktif</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-sm font-bold" style="color: var(--dash-text-heading);">{{ $data['employee_count'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">SDM</p>
            </div>
        </div>
    </div>
</div>
