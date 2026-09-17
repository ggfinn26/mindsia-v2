@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-emerald-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-emerald-600 text-xl">storefront</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Branch Performance</h3>
            <p class="text-xs text-gray-400">{{ $data['branch'] ?? '-' }} · {{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Revenue</p>
                <p class="text-lg font-bold text-gray-900 font-jakarta">Rp {{ number_format($data['revenue'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Margin</p>
                <p class="text-lg font-bold {{ ($data['margin'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }} font-jakarta">
                    {{ $data['margin_percent'] ?? 0 }}%
                </p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-50">
            <div class="text-center">
                <p class="text-sm font-bold text-gray-900">{{ $data['active_members'] ?? 0 }}</p>
                <p class="text-xs text-gray-400">Member Aktif</p>
            </div>
            <div class="text-center">
                <p class="text-sm font-bold text-gray-900">{{ $data['active_classes'] ?? 0 }}</p>
                <p class="text-xs text-gray-400">Kelas Aktif</p>
            </div>
            <div class="text-center">
                <p class="text-sm font-bold text-gray-900">{{ $data['employee_count'] ?? 0 }}</p>
                <p class="text-xs text-gray-400">SDM</p>
            </div>
        </div>
    </div>
</div>
