@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-rose-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-rose-500 text-xl">pie_chart</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Cost Analysis</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div>
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Total Cost</p>
            <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">Rp {{ number_format($data['total_cost'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="space-y-1.5 pt-2 border-t border-gray-50">
            @foreach($data['breakdown'] ?? [] as $category => $amount)
                <div class="flex items-center justify-between">
                    <span class="text-xs text-[color:var(--dash-text-muted)] truncate max-w-[55%]">{{ $category }}</span>
                    <span class="text-xs font-semibold text-[color:var(--dash-text-heading)]">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
