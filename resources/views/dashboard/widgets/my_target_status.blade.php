@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-amber-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-amber-600 text-xl">flag</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">My Target Status</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Target</p>
                <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">{{ $data['target'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Actual</p>
                <p class="text-2xl font-bold {{ ($data['actual'] ?? 0) >= ($data['target'] ?? 0) ? 'text-emerald-600' : 'text-amber-600' }} font-jakarta">{{ $data['actual'] ?? 0 }}</p>
            </div>
        </div>
        <div class="pt-2 border-t border-gray-50">
            <div class="flex justify-between text-xs text-[color:var(--dash-text-faint)] mb-1">
                <span>Progress</span>
                <span class="font-medium {{ ($data['achievement_percent'] ?? 0) >= 100 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $data['achievement_percent'] ?? 0 }}%</span>
            </div>
            <div class="h-2 bg-[var(--dash-nav-hover-bg)] rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ ($data['achievement_percent'] ?? 0) >= 100 ? 'bg-emerald-500' : 'bg-amber-500' }}"
                     style="width: {{ min($data['achievement_percent'] ?? 0, 100) }}%"></div>
            </div>
        </div>
        @if(isset($data['sisa_target']) && $data['sisa_target'] > 0)
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center">Butuh <strong class="text-[color:var(--dash-text-body)]">{{ $data['sisa_target'] }}</strong> lagi untuk capai target</p>
        @endif
    </div>
</div>
