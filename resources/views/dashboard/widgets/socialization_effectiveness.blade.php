@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">groups</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Socialization Effectiveness</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 flex-grow">
        <div class="text-center p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $data['total_events'] ?? 0 }}</p>
            <p class="text-xs mt-1 font-medium" style="color: var(--dash-text-muted);">Events</p>
        </div>
        <div class="text-center p-3 rounded-lg" style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-primary-text);">{{ $data['total_attended'] ?? 0 }}</p>
            <p class="text-xs mt-1 font-medium" style="color: var(--dash-text-muted);">Hadir</p>
        </div>
        <div class="text-center p-3 rounded-lg" style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-status-success-text);">{{ $data['converted'] ?? 0 }}</p>
            <p class="text-xs mt-1 font-medium" style="color: var(--dash-text-muted);">Converted</p>
        </div>
        <div class="text-center p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-primary-text);">{{ $data['conversion_rate'] ?? 0 }}%</p>
            <p class="text-xs mt-1 font-medium" style="color: var(--dash-text-muted);">Conv. Rate</p>
        </div>
    </div>
</div>
