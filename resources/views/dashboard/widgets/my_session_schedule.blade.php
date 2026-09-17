@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-sky-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-sky-600 text-xl">event_note</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">My Session Schedule</h3>
            <p class="text-xs text-gray-400">Mendatang</p>
        </div>
    </div>
    <div class="space-y-2">
        @forelse($data['upcoming'] ?? [] as $session)
            <div class="flex items-start gap-2 p-2 rounded-lg bg-gray-50">
                <div class="text-center min-w-[36px] bg-white rounded p-1 border border-gray-100">
                    <p class="text-xs font-bold text-gray-900">{{ \Carbon\Carbon::parse($session['scheduled_date'])->format('d') }}</p>
                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($session['scheduled_date'])->format('M') }}</p>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $session['class_name'] ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $session['start_time'] ?? '' }} · {{ $session['member_count'] ?? 0 }} siswa</p>
                </div>
            </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-4">Tidak ada sesi mendatang</p>
        @endforelse
        @if(isset($data['total_upcoming']) && $data['total_upcoming'] > count($data['upcoming'] ?? []))
            <p class="text-xs text-gray-400 text-center">+{{ $data['total_upcoming'] - count($data['upcoming']) }} sesi lainnya</p>
        @endif
    </div>
</div>
