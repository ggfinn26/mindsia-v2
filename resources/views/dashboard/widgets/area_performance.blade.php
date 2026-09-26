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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        @forelse($data['areas'] ?? [] as $area)
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100/50 hover:bg-gray-100/50 transition-colors">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-800">{{ $area['area'] }}</span>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">Rp {{ number_format($area['revenue'] / 1000000, 1) }} Juta</span>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-gray-400">store</span>
                        <span>{{ $area['branch_count'] }} cabang</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-gray-400">person_add</span>
                        <span>{{ $area['new_members'] }} baru</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <p class="text-xs text-gray-400 text-center py-4">Tidak ada data</p>
            </div>
        @endforelse
    </div>
</div>
