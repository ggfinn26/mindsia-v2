@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
            <span class="material-symbols-outlined text-xl">flag</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">My Target Status</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Target</p>
                <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $data['target'] ?? 0 }}</p>
            </div>
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Actual</p>
                <p class="text-2xl font-bold font-jakarta" style="color: {{ ($data['actual'] ?? 0) >= ($data['target'] ?? 0) ? 'var(--dash-status-success-text)' : 'var(--dash-status-warning-text)' }};">{{ $data['actual'] ?? 0 }}</p>
            </div>
        </div>
        <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="flex justify-between text-xs mb-1.5" style="color: var(--dash-text-muted);">
                <span>Progress</span>
                <span class="font-bold" style="color: {{ ($data['achievement_percent'] ?? 0) >= 100 ? 'var(--dash-status-success-text)' : 'var(--dash-status-warning-text)' }};">{{ $data['achievement_percent'] ?? 0 }}%</span>
            </div>
            <div class="h-2 rounded-full overflow-hidden" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <div class="h-full rounded-full transition-all"
                     style="width: {{ min($data['achievement_percent'] ?? 0, 100) }}%; background-color: {{ ($data['achievement_percent'] ?? 0) >= 100 ? 'var(--dash-status-success-text)' : 'var(--dash-status-warning-text)' }};"></div>
            </div>
        </div>
        @if(isset($data['sisa_target']) && $data['sisa_target'] > 0)
            <p class="text-xs text-center" style="color: var(--dash-text-muted);">Butuh <strong style="color: var(--dash-text-heading);">{{ $data['sisa_target'] }}</strong> lagi untuk capai target</p>
        @endif
    </div>
</div>
