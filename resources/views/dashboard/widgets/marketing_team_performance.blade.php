@props(['data'])

<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-purple-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-purple-600 text-xl">leaderboard</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Marketing Team Performance</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Team Target</p>
                <p class="text-xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">{{ $data['team_target_member'] ?? 0 }} <span class="text-xs text-[color:var(--dash-text-faint)] font-normal">member</span></p>
            </div>
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Achievement</p>
                <p class="text-xl font-bold {{ ($data['team_achievement_percent'] ?? 0) >= 100 ? 'text-emerald-600' : 'text-yellow-600' }} font-jakarta">{{ $data['team_achievement_percent'] ?? 0 }}%</p>
            </div>
        </div>

        @if(!empty($data['ranking']))
            <div class="pt-2 border-t border-gray-50">
                <p class="text-xs text-[color:var(--dash-text-faint)] mb-2">Ranking</p>
                <div class="space-y-1.5">
                    @foreach(array_slice($data['ranking'], 0, 5) as $i => $emp)
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[color:var(--dash-text-faint)] w-4">{{ $i + 1 }}</span>
                            <span class="text-xs text-[color:var(--dash-text-body)] flex-1 truncate">{{ $emp['employee'] }}</span>
                            <span class="text-xs font-semibold text-[color:var(--dash-text-heading)]">{{ $emp['total_member'] }}</span>
                            @if($emp['achievement_percent'] !== null)
                                <span class="text-xs {{ $emp['achievement_percent'] >= 100 ? 'text-emerald-600' : 'text-yellow-500' }}">{{ $emp['achievement_percent'] }}%</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
