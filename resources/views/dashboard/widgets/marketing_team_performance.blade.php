@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">leaderboard</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Marketing Team Performance</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Team Target</p>
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $data['team_target_member'] ?? 0 }} <span class="text-xs font-normal" style="color: var(--dash-text-muted);">member</span></p>
            </div>
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Achievement</p>
                <p class="text-xl font-bold font-jakarta" style="color: {{ ($data['team_achievement_percent'] ?? 0) >= 100 ? 'var(--dash-status-success-text)' : 'var(--dash-status-warning-text)' }};">{{ $data['team_achievement_percent'] ?? 0 }}%</p>
            </div>
        </div>

        @if(!empty($data['ranking']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Ranking</p>
                <div class="space-y-1.5">
                    @foreach(array_slice($data['ranking'], 0, 5) as $i => $emp)
                        <div class="flex items-center gap-2 text-xs">
                            <span class="font-bold w-4 text-center" style="color: {{ $i === 0 ? 'var(--dash-primary-text)' : 'var(--dash-text-muted)' }};">{{ $i + 1 }}</span>
                            <span class="flex-1 truncate" style="color: var(--dash-text-body);">{{ $emp['employee'] }}</span>
                            <span class="font-bold" style="color: var(--dash-text-heading);">{{ $emp['total_member'] }}</span>
                            @if($emp['achievement_percent'] !== null)
                                <span class="font-semibold" style="color: {{ $emp['achievement_percent'] >= 100 ? 'var(--dash-status-success-text)' : 'var(--dash-status-warning-text)' }};">{{ $emp['achievement_percent'] }}%</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
