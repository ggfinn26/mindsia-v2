@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
            <span class="material-symbols-outlined text-xl">emoji_events</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">My Ranking</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-3 gap-2 flex-grow items-center">
        <div class="text-center p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xl sm:text-2xl font-bold font-jakarta" style="color: var(--dash-primary-text);">#{{ $data['branch_rank'] ?? '-' }}</p>
            <p class="text-xs font-semibold mt-0.5" style="color: var(--dash-text-heading);">Cabang</p>
            <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">dari {{ $data['branch_total'] ?? '-' }}</p>
        </div>
        <div class="text-center p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xl sm:text-2xl font-bold font-jakarta" style="color: var(--dash-status-warning-text);">#{{ $data['area_rank'] ?? '-' }}</p>
            <p class="text-xs font-semibold mt-0.5" style="color: var(--dash-text-heading);">Area</p>
            <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">dari {{ $data['area_total'] ?? '-' }}</p>
        </div>
        <div class="text-center p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xl sm:text-2xl font-bold font-jakarta" style="color: var(--dash-status-danger-text);">#{{ $data['national_rank'] ?? '-' }}</p>
            <p class="text-xs font-semibold mt-0.5" style="color: var(--dash-text-heading);">Nasional</p>
            <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">dari {{ $data['national_total'] ?? '-' }}</p>
        </div>
    </div>
</div>
