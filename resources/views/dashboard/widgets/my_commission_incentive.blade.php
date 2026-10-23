@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-xl">savings</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">My Commission & Incentive</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Total Komisi & Bonus</p>
            <p class="text-2xl sm:text-3xl font-bold font-jakarta tracking-tight" style="color: var(--dash-status-success-text);">Rp {{ number_format($data['total_commission_bonus'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="grid grid-cols-2 gap-2 pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="p-2.5 rounded-lg text-center" style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Komisi</p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--dash-status-success-text);">Rp {{ number_format($data['commission'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-2.5 rounded-lg text-center" style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Bonus</p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--dash-primary-text);">Rp {{ number_format($data['bonus'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
