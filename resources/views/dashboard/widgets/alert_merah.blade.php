@if(isset($data['error']))
    <div class="p-4 rounded-2xl" style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text); border: 1px solid var(--dash-card-border);">Error: {{ $data['error'] }}</div>
@else
<div class="rounded-2xl transition-all p-5 h-full flex flex-col" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text);">
                <span class="material-symbols-outlined text-lg">error</span>
            </div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Critical Alerts</h3>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">{{ $data['period'] }}</span>
    </div>

    @if(($data['total_alerts'] ?? 0) === 0)
        <div class="flex-1 flex flex-col items-center justify-center py-6 text-center">
            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3" style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                <span class="material-symbols-outlined text-2xl">check_circle</span>
            </div>
            <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">Tidak Ada Isu Kritis</p>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Semua operasional berjalan normal.</p>
        </div>
    @else
        <div class="flex items-center gap-4 mb-4 pb-4 border-b" style="border-color: var(--dash-card-border);">
            <div class="flex-1">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Danger</p>
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-status-danger-text);">{{ $data['danger_count'] }}</p>
            </div>
            <div class="w-px h-8" style="background-color: var(--dash-card-border);"></div>
            <div class="flex-1">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Warning</p>
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-status-warning-text);">{{ $data['warning_count'] }}</p>
            </div>
        </div>

        <div class="space-y-2.5 flex-1 overflow-y-auto custom-scrollbar pr-1 max-h-[250px]">
            @foreach($data['alerts'] as $alert)
                <div class="flex items-start justify-between p-3 rounded-lg border transition-colors" style="background-color: {{ $alert['severity'] === 'danger' ? 'var(--dash-status-danger-bg)' : 'var(--dash-status-warning-bg)' }}; border-color: var(--dash-card-border);">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-base mt-0.5" style="color: {{ $alert['severity'] === 'danger' ? 'var(--dash-status-danger-text)' : 'var(--dash-status-warning-text)' }};">
                            {{ $alert['severity'] === 'danger' ? 'warning' : 'info' }}
                        </span>
                        <div>
                            <p class="text-xs font-semibold" style="color: var(--dash-text-heading);">{{ $alert['label'] }}</p>
                            <a href="#" class="text-xs font-medium hover:underline mt-1 inline-block" style="color: {{ $alert['severity'] === 'danger' ? 'var(--dash-status-danger-text)' : 'var(--dash-status-warning-text)' }};">
                                Tindak Lanjuti &rarr;
                            </a>
                        </div>
                    </div>
                    <span class="text-sm font-bold ml-2" style="color: {{ $alert['severity'] === 'danger' ? 'var(--dash-status-danger-text)' : 'var(--dash-status-warning-text)' }};">
                        {{ $alert['count'] }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endif
