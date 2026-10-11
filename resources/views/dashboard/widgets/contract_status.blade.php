@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg {{ ($data['expiring_soon'] ?? 0) > 0 ? 'bg-amber-50' : 'bg-sky-50' }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ ($data['expiring_soon'] ?? 0) > 0 ? 'text-amber-600' : 'text-sky-600' }} text-xl">description</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Contract Status</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(($data['expiring_soon'] ?? 0) > 0)
            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                {{ $data['expiring_soon'] }} segera berakhir
            </span>
        @endif
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Kontrak Aktif</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['active_contracts'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Diperpanjang</p>
                <p class="text-sm font-semibold text-blue-600">{{ $data['extended_contracts'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Ditangguhkan</p>
                <p class="text-sm font-semibold text-amber-600">{{ $data['suspended_contracts'] ?? 0 }}</p>
            </div>
        </div>

        @if(($data['pending_extensions'] ?? 0) > 0)
        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-gray-400">Permohonan Perpanjangan</span>
            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">{{ $data['pending_extensions'] }}</span>
        </div>
        @endif

        @if(!empty($data['by_status']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Distribusi Status</p>
            <div class="space-y-1">
                @foreach($data['by_status'] as $status => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ ucfirst($status) }}</span>
                        <span class="text-xs font-medium text-gray-700">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
