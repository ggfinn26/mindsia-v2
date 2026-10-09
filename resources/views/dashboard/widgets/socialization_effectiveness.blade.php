@props(['data'])

<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-teal-600 text-xl">groups</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Socialization Effectiveness</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div class="text-center p-3 rounded-lg bg-[var(--dash-nav-hover-bg)]">
            <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">{{ $data['total_events'] ?? 0 }}</p>
            <p class="text-xs text-[color:var(--dash-text-faint)] mt-1">Events</p>
        </div>
        <div class="text-center p-3 rounded-lg bg-blue-50">
            <p class="text-2xl font-bold text-blue-600 font-jakarta">{{ $data['total_attended'] ?? 0 }}</p>
            <p class="text-xs text-[color:var(--dash-text-faint)] mt-1">Hadir</p>
        </div>
        <div class="text-center p-3 rounded-lg bg-emerald-50">
            <p class="text-2xl font-bold text-emerald-600 font-jakarta">{{ $data['converted'] ?? 0 }}</p>
            <p class="text-xs text-[color:var(--dash-text-faint)] mt-1">Converted</p>
        </div>
        <div class="text-center p-3 rounded-lg bg-purple-50">
            <p class="text-2xl font-bold text-purple-600 font-jakarta">{{ $data['conversion_rate'] ?? 0 }}%</p>
            <p class="text-xs text-[color:var(--dash-text-faint)] mt-1">Conv. Rate</p>
        </div>
    </div>
</div>
