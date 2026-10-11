@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-red-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-600 text-xl">gavel</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Compliance & Policies</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">SOP Aktif</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['active_sop_count'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Kebijakan Aktif</p>
                <p class="text-sm font-semibold text-gray-700">{{ $data['active_policies'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">SP Aktif</p>
                <p class="text-sm font-semibold text-red-500">{{ $data['active_warning_letters'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Pelanggaran Bulan Ini</p>
                <p class="text-sm font-semibold text-amber-600">{{ $data['violations_this_month'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Kebijakan Exempt</p>
                <p class="text-sm font-semibold text-gray-700">{{ $data['exempt_policies'] ?? 0 }}</p>
            </div>
        </div>

        @if(!empty($data['by_sp_level']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Distribusi SP</p>
            @foreach($data['by_sp_level'] as $level => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600">SP{{ $level }}</span>
                    <span class="text-xs font-semibold {{ $level >= 3 ? 'text-red-500' : 'text-amber-600' }}">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
