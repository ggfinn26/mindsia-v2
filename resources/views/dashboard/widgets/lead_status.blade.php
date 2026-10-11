@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-orange-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-orange-500 text-xl">funnel</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Lead Status</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Total Prospek</p>
                <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Closing Rate</p>
                <p class="text-2xl font-bold text-emerald-600 font-jakarta">{{ $data['closing_rate'] ?? 0 }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-50">
            <div class="text-center p-2 rounded-lg bg-yellow-50">
                <p class="text-lg font-bold text-yellow-600">{{ $data['follow_up_pending'] ?? 0 }}</p>
                <p class="text-xs text-gray-400">Follow-up</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-blue-50">
                <p class="text-lg font-bold text-blue-600">{{ $data['new_this_month'] ?? 0 }}</p>
                <p class="text-xs text-gray-400">Baru Bulan Ini</p>
            </div>
        </div>
    </div>
</div>
