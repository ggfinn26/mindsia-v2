@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-violet-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-violet-600 text-xl">school</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Program Financial Performance</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-2">
        @forelse($data['programs'] ?? [] as $program)
            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50">
                <span class="text-xs font-medium text-gray-700 truncate max-w-[55%]">{{ $program['program'] }}</span>
                <div class="text-right">
                    <p class="text-xs font-bold text-gray-900">Rp {{ number_format($program['revenue'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">{{ $program['member_count'] }} member</p>
                </div>
            </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-4">Tidak ada data</p>
        @endforelse
    </div>
</div>
