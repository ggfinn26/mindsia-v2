@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-green-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-green-600 text-xl">savings</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">My Commission & Incentive</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Total Komisi & Bonus</p>
            <p class="text-2xl font-bold text-emerald-600 font-jakarta">Rp {{ number_format($data['total_commission_bonus'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-50">
            <div class="p-2 rounded-lg bg-emerald-50">
                <p class="text-xs text-gray-400">Komisi</p>
                <p class="text-sm font-bold text-emerald-700">Rp {{ number_format($data['commission'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-2 rounded-lg bg-blue-50">
                <p class="text-xs text-gray-400">Bonus</p>
                <p class="text-sm font-bold text-blue-700">Rp {{ number_format($data['bonus'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
