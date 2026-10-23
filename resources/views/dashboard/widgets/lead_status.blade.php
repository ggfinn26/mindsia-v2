@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">filter_alt</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Lead Status</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Total Prospek</p>
                <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $data['total'] ?? 0 }}</p>
            </div>
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Closing Rate</p>
                <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-status-success-text);">{{ $data['closing_rate'] ?? 0 }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-lg font-bold font-jakarta" style="color: var(--dash-status-warning-text);">{{ $data['follow_up_pending'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Follow-up</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-lg font-bold font-jakarta" style="color: var(--dash-primary-text);">{{ $data['new_this_month'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Baru Bulan Ini</p>
            </div>
        </div>
    </div>
</div>
