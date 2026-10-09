@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-sky-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-sky-600 text-xl">calendar_month</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Socialization Schedule</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-2">
        @if(isset($data['upcoming_count']))
            <div class="flex items-center gap-2 p-2 rounded-lg bg-blue-50">
                <span class="material-symbols-outlined text-blue-500 text-base">upcoming</span>
                <span class="text-xs text-blue-700 font-medium">{{ $data['upcoming_count'] }} event mendatang</span>
            </div>
        @endif
        @forelse($data['upcoming'] ?? [] as $event)
            <div class="flex items-start gap-2 p-2 rounded-lg bg-[var(--dash-nav-hover-bg)]">
                <div class="text-center min-w-[36px] bg-[var(--dash-card-bg)] rounded p-1 border border-[color:var(--dash-card-border)]">
                    <p class="text-xs font-bold text-[color:var(--dash-text-heading)]">{{ \Carbon\Carbon::parse($event['schedule_date'])->format('d') }}</p>
                    <p class="text-xs text-[color:var(--dash-text-faint)]">{{ \Carbon\Carbon::parse($event['schedule_date'])->format('M') }}</p>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-[color:var(--dash-text-heading)] truncate">{{ $event['location'] ?? '-' }}</p>
                    <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $event['branch'] ?? '-' }}</p>
                </div>
            </div>
        @empty
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center py-4">Tidak ada jadwal</p>
        @endforelse
    </div>
</div>
