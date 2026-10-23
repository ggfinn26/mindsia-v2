@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
            <span class="material-symbols-outlined text-xl">receipt</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Invoice & Bill Management</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3 flex-grow">
        <div class="p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Incoming (Paid)</p>
            <p class="text-sm font-bold font-jakarta" style="color: var(--dash-status-success-text);">Rp {{ number_format($data['incoming_paid'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Incoming (Pending)</p>
            <p class="text-sm font-bold font-jakarta" style="color: var(--dash-status-warning-text);">Rp {{ number_format($data['incoming_pending'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Outgoing (Paid)</p>
            <p class="text-sm font-bold font-jakarta" style="color: var(--dash-status-danger-text);">Rp {{ number_format($data['outgoing_paid'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Outgoing (Pending)</p>
            <p class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Rp {{ number_format($data['outgoing_pending'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>
</div>
