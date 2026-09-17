@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-sky-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-sky-600 text-xl">calendar_month</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Socialization Schedule</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
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
            <div class="flex items-start gap-2 p-2 rounded-lg bg-gray-50">
                <div class="text-center min-w-[36px] bg-white rounded p-1 border border-gray-100">
                    <p class="text-xs font-bold text-gray-900">{{ \Carbon\Carbon::parse($event['scheduled_date'])->format('d') }}</p>
                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($event['scheduled_date'])->format('M') }}</p>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $event['title'] }}</p>
                    <p class="text-xs text-gray-400">{{ $event['location'] ?? '-' }}</p>
                </div>
            </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-4">Tidak ada jadwal</p>
        @endforelse
    </div>
</div>
