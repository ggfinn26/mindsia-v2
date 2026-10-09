@props(['data'])

<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-purple-50 [.mode-dark_&]:bg-purple-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600 [.mode-dark_&]:text-purple-400 text-xl">campaign</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[var(--dash-text-heading)]">Marketing Revenue</h3>
                <p class="text-xs text-[var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['trend_percent']))
            <div class="flex items-center gap-1 {{ $data['trend_percent'] >= 0 ? 'text-emerald-600 [.mode-dark_&]:text-emerald-400' : 'text-red-500 [.mode-dark_&]:text-red-400' }}">
                <span class="material-symbols-outlined text-sm">{{ $data['trend_percent'] >= 0 ? 'trending_up' : 'trending_down' }}</span>
                <span class="text-xs font-semibold">{{ abs($data['trend_percent']) }}%</span>
            </div>
        @endif
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-[var(--dash-text-faint)] mb-1">Total Revenue</p>
            <p class="text-2xl font-bold text-[var(--dash-text-heading)] font-jakarta">Rp {{ number_format($data['total_revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>

        @if(!empty($data['by_employee']))
            <div class="pt-2 border-t border-[var(--dash-card-border)]">
                <p class="text-xs text-[var(--dash-text-faint)] mb-2">Top Marketer</p>
                <div class="space-y-1">
                    @foreach(array_slice($data['by_employee'], 0, 3) as $emp)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-[var(--dash-text-muted)] truncate max-w-[60%]">{{ $emp['employee'] }}</span>
                            <span class="text-xs font-semibold text-[var(--dash-text-heading)]">Rp {{ number_format($emp['amount'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
