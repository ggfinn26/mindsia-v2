@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-sky-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-sky-600 text-xl">event_note</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">My Session Schedule</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">Mendatang</p>
        </div>
    </div>
    <div class="space-y-2">
        @forelse($data['upcoming'] ?? [] as $session)
            <div class="flex items-start gap-2 p-2 rounded-lg bg-[var(--dash-nav-hover-bg)]">
                <div class="text-center min-w-[36px] bg-[var(--dash-card-bg)] rounded p-1 border border-[color:var(--dash-card-border)]">
                    <p class="text-xs font-bold text-[color:var(--dash-text-heading)]">{{ \Carbon\Carbon::parse($session['scheduled_date'])->format('d') }}</p>
                    <p class="text-xs text-[color:var(--dash-text-faint)]">{{ \Carbon\Carbon::parse($session['scheduled_date'])->format('M') }}</p>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-[color:var(--dash-text-heading)] truncate">{{ $session['class_name'] ?? '-' }}</p>
                    <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $session['start_time'] ?? '' }} · {{ $session['member_count'] ?? 0 }} siswa</p>
                </div>
            </div>
        @empty
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center py-4">Tidak ada sesi mendatang</p>
        @endforelse
        @if(isset($data['total_upcoming']) && $data['total_upcoming'] > count($data['upcoming'] ?? []))
            <p class="text-xs text-[color:var(--dash-text-faint)] text-center">+{{ $data['total_upcoming'] - count($data['upcoming']) }} sesi lainnya</p>
        @endif
    </div>
</div>
