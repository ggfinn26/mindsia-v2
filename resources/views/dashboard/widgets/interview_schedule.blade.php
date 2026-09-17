@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-xl">event</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Interview Schedule</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Total Interview</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total_interviews'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Akan Datang</p>
                <p class="text-sm font-semibold text-blue-600">{{ $data['upcoming'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Selesai</p>
                <p class="text-sm font-semibold text-emerald-600">{{ $data['completed'] ?? 0 }}</p>
            </div>
        </div>

        @if(!empty($data['by_type']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Tipe Interview</p>
            @foreach($data['by_type'] as $type => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600">{{ ucfirst($type) }}</span>
                    <span class="text-xs font-semibold text-gray-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif

        @if(!empty($data['by_decision']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Keputusan</p>
            @foreach($data['by_decision'] as $decision => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600">{{ ucfirst($decision) }}</span>
                    <span class="text-xs font-semibold {{ $decision === 'accepted' ? 'text-emerald-600' : ($decision === 'rejected' ? 'text-red-500' : 'text-amber-600') }}">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
