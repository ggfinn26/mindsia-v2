@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-green-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-green-600 text-xl">store</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Branch Financial Performance</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-2">
        @forelse($data['branches'] ?? [] as $branch)
            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50">
                <span class="text-xs font-medium text-gray-700 truncate max-w-[45%]">{{ $branch['branch'] }}</span>
                <div class="text-right">
                    <p class="text-xs font-bold text-gray-900">Rp {{ number_format($branch['revenue'], 0, ',', '.') }}</p>
                    <p class="text-xs {{ $branch['margin'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $branch['margin_percent'] }}%</p>
                </div>
            </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-4">Tidak ada data</p>
        @endforelse
    </div>
</div>
