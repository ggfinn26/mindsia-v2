@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">event_note</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">My Session Schedule</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">Mendatang</p>
        </div>
    </div>
    <div class="space-y-2 flex-grow">
        @forelse($data['upcoming'] ?? [] as $session)
            <div class="flex items-start gap-2.5 p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <div class="text-center min-w-[36px] rounded p-1 border shrink-0" style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                    <p class="text-xs font-bold" style="color: var(--dash-text-heading);">{{ \Carbon\Carbon::parse($session['scheduled_date'])->format('d') }}</p>
                    <p class="text-[10px] uppercase font-semibold" style="color: var(--dash-text-muted);">{{ \Carbon\Carbon::parse($session['scheduled_date'])->format('M') }}</p>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold truncate" style="color: var(--dash-text-heading);">{{ $session['class_name'] ?? '-' }}</p>
                    <p class="text-[11px]" style="color: var(--dash-text-muted);">{{ $session['start_time'] ?? '' }} · {{ $session['member_count'] ?? 0 }} siswa</p>
                </div>
            </div>
        @empty
            <div class="py-4 text-center">
                <p class="text-xs" style="color: var(--dash-text-muted);">Tidak ada sesi mendatang</p>
            </div>
        @endforelse
        @if(isset($data['total_upcoming']) && $data['total_upcoming'] > count($data['upcoming'] ?? []))
            <p class="text-xs text-center font-medium mt-1" style="color: var(--dash-primary-text);">+{{ $data['total_upcoming'] - count($data['upcoming']) }} sesi lainnya</p>
        @endif
    </div>
</div>
