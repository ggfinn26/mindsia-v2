@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-orange-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-orange-500 text-xl">receipt</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Invoice & Bill Management</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div class="p-3 rounded-lg bg-emerald-50">
            <p class="text-xs text-gray-400 mb-1">Incoming (Paid)</p>
            <p class="text-sm font-bold text-emerald-700">Rp {{ number_format($data['incoming_paid'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-lg bg-yellow-50">
            <p class="text-xs text-gray-400 mb-1">Incoming (Pending)</p>
            <p class="text-sm font-bold text-yellow-700">Rp {{ number_format($data['incoming_pending'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-lg bg-red-50">
            <p class="text-xs text-gray-400 mb-1">Outgoing (Paid)</p>
            <p class="text-sm font-bold text-red-600">Rp {{ number_format($data['outgoing_paid'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-lg bg-gray-50">
            <p class="text-xs text-gray-400 mb-1">Outgoing (Pending)</p>
            <p class="text-sm font-bold text-gray-700">Rp {{ number_format($data['outgoing_pending'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>
</div>
