@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">analytics</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Marketing KPI Snapshot</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Avg Score</p>
                <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $data['avg_score'] ?? 0 }}</p>
            </div>
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Dievaluasi</p>
                <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $data['total_employees_evaluated'] ?? 0 }}</p>
            </div>
        </div>

        @if(!empty($data['by_grade']))
            <div class="flex flex-wrap gap-1.5 pt-2 border-t" style="border-color: var(--dash-card-border);">
                @foreach($data['by_grade'] as $grade => $count)
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full font-semibold" style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);">
                        Grade {{ $grade }}: <span style="color: var(--dash-text-heading);">{{ $count }}</span>
                    </span>
                @endforeach
            </div>
        @endif

        @if(!empty($data['top_ranking']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Top Performer</p>
                <div class="space-y-1">
                    @foreach(array_slice($data['top_ranking'], 0, 3) as $i => $item)
                        <div class="flex items-center gap-2 text-xs">
                            <span class="font-bold w-4 text-center" style="color: {{ $i === 0 ? 'var(--dash-primary-text)' : 'var(--dash-text-muted)' }};">{{ $i + 1 }}</span>
                            <span class="flex-1 truncate" style="color: var(--dash-text-body);">{{ $item['employee'] }}</span>
                            <span class="font-bold" style="color: var(--dash-primary-text);">{{ $item['total_score'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
