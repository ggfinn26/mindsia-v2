@props(['data'])

@php
    $conditionLabels = ['GOOD' => 'Baik', 'NEEDS_REPAIR' => 'Perlu Perbaikan', 'DAMAGED' => 'Rusak', 'UNUSABLE' => 'Tidak Pakai'];
    $typeLabels = ['FIXED_ASSET' => 'Aset Tetap', 'SUPPLIES' => 'Perlengkapan', 'OTHER' => 'Lainnya'];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg {{ ($data['needs_repair'] ?? 0) > 0 ? 'bg-amber-50' : 'bg-cyan-50' }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ ($data['needs_repair'] ?? 0) > 0 ? 'text-amber-600' : 'text-cyan-600' }} text-xl">inventory_2</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Inventory Status</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(($data['needs_repair'] ?? 0) > 0 || ($data['unusable'] ?? 0) > 0)
            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                {{ ($data['needs_repair'] ?? 0) + ($data['unusable'] ?? 0) }} perlu atensi
            </span>
        @endif
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Item Aktif</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['active_items'] ?? 0 }}</p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-gray-400">Nilai Total</span>
            <span class="text-sm font-semibold text-gray-700">Rp {{ number_format($data['total_value'] ?? 0, 0, ',', '.') }}</span>
        </div>

        @if(!empty($data['by_condition']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Kondisi</p>
            <div class="space-y-1">
                @foreach($data['by_condition'] as $condition => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $conditionLabels[$condition] ?? ucfirst(str_replace('_', ' ', $condition)) }}</span>
                        <span class="text-xs font-medium {{ in_array($condition, ['DAMAGED', 'UNUSABLE']) ? 'text-red-600' : 'text-gray-700' }}">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($data['by_type']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Jenis</p>
            <div class="space-y-1">
                @foreach($data['by_type'] as $type => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}</span>
                        <span class="text-xs font-medium text-gray-700">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
