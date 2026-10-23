@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-xl">workspace_premium</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">My Performance Summary</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['grade']))
            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">Grade {{ $data['grade'] }}</span>
        @endif
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="text-center p-2.5 rounded-lg" style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-primary);">
                <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-primary-text);">{{ $data['total_score'] ?? '-' }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Total Score</p>
            </div>
            <div class="text-center p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ ($data['rank_branch'] ?? null) ? '#'.$data['rank_branch'] : '-' }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Rank Cabang</p>
            </div>
        </div>
        @if(!empty($data['evaluations']))
            <div class="pt-2 border-t space-y-1.5" style="border-color: var(--dash-card-border);">
                @foreach(array_slice($data['evaluations'], 0, 3) as $eval)
                    <div class="flex justify-between items-center text-xs">
                        <span class="truncate max-w-[60%]" style="color: var(--dash-text-body);">{{ $eval['category'] }}</span>
                        <span class="font-bold" style="color: var(--dash-primary-text);">{{ $eval['score'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
