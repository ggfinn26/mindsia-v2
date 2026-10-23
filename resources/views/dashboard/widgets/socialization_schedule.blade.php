@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">calendar_month</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Socialization Schedule</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-2 flex-grow">
        @if(isset($data['upcoming_count']))
            <div class="flex items-center gap-2 p-2 rounded-lg" style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-primary);">
                <span class="material-symbols-outlined text-base" style="color: var(--dash-primary-text);">upcoming</span>
                <span class="text-xs font-semibold" style="color: var(--dash-primary-text);">{{ $data['upcoming_count'] }} event mendatang</span>
            </div>
        @endif
        @forelse($data['upcoming'] ?? [] as $event)
            <div class="flex items-start gap-2.5 p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <div class="text-center min-w-[36px] rounded p-1 border shrink-0" style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                    <p class="text-xs font-bold" style="color: var(--dash-text-heading);">{{ \Carbon\Carbon::parse($event['schedule_date'])->format('d') }}</p>
                    <p class="text-[10px] uppercase font-semibold" style="color: var(--dash-text-muted);">{{ \Carbon\Carbon::parse($event['schedule_date'])->format('M') }}</p>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold truncate" style="color: var(--dash-text-heading);">{{ $event['location'] ?? '-' }}</p>
                    <p class="text-[11px]" style="color: var(--dash-text-muted);">{{ $event['branch'] ?? '-' }}</p>
                </div>
            </div>
        @empty
            <div class="py-4 text-center">
                <p class="text-xs" style="color: var(--dash-text-muted);">Tidak ada jadwal</p>
            </div>
        @endforelse
    </div>
</div>
