@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-amber-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-amber-600 text-xl">balance</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Budget vs Actual</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Budget</p>
                <p class="text-lg font-bold text-gray-900 font-jakarta">Rp {{ number_format($data['budget'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Actual</p>
                <p class="text-lg font-bold text-gray-900 font-jakarta">Rp {{ number_format($data['actual'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="pt-2 border-t border-gray-50">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-400">Variance</p>
                <p class="text-sm font-bold {{ ($data['variance'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ ($data['variance'] ?? 0) >= 0 ? '+' : '' }}Rp {{ number_format($data['variance'] ?? 0, 0, ',', '.') }}
                    <span class="text-xs">({{ ($data['variance'] ?? 0) >= 0 ? '+' : '' }}{{ $data['variance_percent'] ?? 0 }}%)</span>
                </p>
            </div>
        </div>
    </div>
</div>
