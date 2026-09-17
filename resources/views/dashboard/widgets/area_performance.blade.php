@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-indigo-600 text-xl">map</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Area Performance</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-2">
        @forelse($data['areas'] ?? [] as $area)
            <div class="p-2 rounded-lg bg-gray-50">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-gray-700">{{ $area['area'] }}</span>
                    <span class="text-xs font-bold text-indigo-600">Rp {{ number_format($area['revenue'] / 1000000, 1) }}M</span>
                </div>
                <div class="flex gap-3 text-xs text-gray-400">
                    <span>{{ $area['branch_count'] }} cabang</span>
                    <span>{{ $area['new_members'] }} member baru</span>
                </div>
            </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-4">Tidak ada data</p>
        @endforelse
    </div>
</div>
