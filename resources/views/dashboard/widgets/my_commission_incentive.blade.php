@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-green-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-green-600 text-xl">savings</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">My Commission & Incentive</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div>
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Total Komisi & Bonus</p>
            <p class="text-2xl font-bold text-emerald-600 font-jakarta">Rp {{ number_format($data['total_commission_bonus'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-50">
            <div class="p-2 rounded-lg bg-emerald-50">
                <p class="text-xs text-[color:var(--dash-text-faint)]">Komisi</p>
                <p class="text-sm font-bold text-emerald-700">Rp {{ number_format($data['commission'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-2 rounded-lg bg-blue-50">
                <p class="text-xs text-[color:var(--dash-text-faint)]">Bonus</p>
                <p class="text-sm font-bold text-blue-700">Rp {{ number_format($data['bonus'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
