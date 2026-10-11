@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-xl">badge</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Employee Directory</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Karyawan Aktif</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total_active'] ?? 0 }}</p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-gray-400">Join Bulan Ini</span>
            <span class="text-sm font-semibold text-emerald-600">{{ $data['recent_joins'] ?? 0 }}</span>
        </div>

        @if(!empty($data['by_position']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Top Posisi</p>
            @foreach($data['by_position'] as $position => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600">{{ $position }}</span>
                    <span class="text-xs font-semibold text-gray-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif

        @if(!empty($data['by_employment_type']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Tipe Karyawan</p>
            @foreach($data['by_employment_type'] as $type => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600">{{ strtoupper($type) }}</span>
                    <span class="text-xs font-semibold text-gray-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
