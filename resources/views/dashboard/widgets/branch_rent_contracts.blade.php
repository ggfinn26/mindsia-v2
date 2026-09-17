@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg {{ ($data['overdue_termins'] ?? 0) > 0 ? 'bg-red-50' : 'bg-purple-50' }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ ($data['overdue_termins'] ?? 0) > 0 ? 'text-red-500' : 'text-purple-600' }} text-xl">real_estate_agent</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Branch Rent Contracts</h3>
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

        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-gray-400">Total Sewa</span>
            <span class="text-sm font-semibold text-gray-700">Rp {{ number_format($data['total_rent_amount'] ?? 0, 0, ',', '.') }}</span>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Termin Dibayar</p>
                <p class="text-sm font-semibold text-emerald-600">Rp {{ number_format($data['total_paid'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Termin Belum Bayar</p>
                <p class="text-sm font-semibold text-red-500">Rp {{ number_format($data['total_unpaid'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        @if(($data['overdue_termins'] ?? 0) > 0)
        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-gray-400">Termin Overdue</span>
            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">{{ $data['overdue_termins'] }}</span>
        </div>
        @endif

        @if(!empty($data['by_status']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Status Kontrak</p>
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
