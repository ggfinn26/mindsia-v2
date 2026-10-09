@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-teal-600 text-xl">handshake</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">My Socialization Activity</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-3 gap-2">
        <div class="text-center p-2 rounded-lg bg-teal-50">
            <p class="text-xl font-bold text-teal-600">{{ $data['events_attended'] ?? 0 }}</p>
            <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">Events</p>
        </div>
        <div class="text-center p-2 rounded-lg bg-blue-50">
            <p class="text-xl font-bold text-blue-600">{{ $data['prospects_met'] ?? 0 }}</p>
            <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">Prospek</p>
        </div>
        <div class="text-center p-2 rounded-lg bg-emerald-50">
            <p class="text-xl font-bold text-emerald-600">{{ $data['converted'] ?? 0 }}</p>
            <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">Closing</p>
        </div>
    </div>
    @if(isset($data['conversion_rate']))
        <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between">
            <span class="text-xs text-[color:var(--dash-text-faint)]">Conversion Rate</span>
            <span class="text-xs font-bold text-teal-600">{{ $data['conversion_rate'] }}%</span>
        </div>
    @endif
</div>
