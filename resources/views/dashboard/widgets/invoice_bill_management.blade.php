@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-orange-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-orange-500 text-xl">receipt</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Invoice & Bill Management</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div class="p-3 rounded-lg bg-emerald-50">
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Incoming (Paid)</p>
            <p class="text-sm font-bold text-emerald-700">Rp {{ number_format($data['incoming_paid'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-lg bg-yellow-50">
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Incoming (Pending)</p>
            <p class="text-sm font-bold text-yellow-700">Rp {{ number_format($data['incoming_pending'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-lg bg-red-50">
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Outgoing (Paid)</p>
            <p class="text-sm font-bold text-red-600">Rp {{ number_format($data['outgoing_paid'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-lg bg-[var(--dash-nav-hover-bg)]">
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Outgoing (Pending)</p>
            <p class="text-sm font-bold text-[color:var(--dash-text-body)]">Rp {{ number_format($data['outgoing_pending'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>
</div>
