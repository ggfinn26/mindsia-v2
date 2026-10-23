@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">bar_chart</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">My Student Progress</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-3 gap-2">
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-primary-text);">{{ $data['total_students'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Total</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-status-success-text);">{{ $data['on_track'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">On Track</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-status-danger-text);">{{ $data['behind'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Behind</p>
            </div>
        </div>
        @if(isset($data['avg_completion_rate']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <div class="flex justify-between text-xs mb-1.5" style="color: var(--dash-text-muted);">
                    <span>Rata-rata Progress</span>
                    <span class="font-semibold" style="color: var(--dash-primary-text);">{{ $data['avg_completion_rate'] }}%</span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <div class="h-full rounded-full transition-all" style="width: {{ min($data['avg_completion_rate'], 100) }}%; background-color: var(--dash-primary);"></div>
                </div>
            </div>
        @endif
    </div>
</div>
