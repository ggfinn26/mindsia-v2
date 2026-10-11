@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-teal-600 text-xl">groups</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Socialization Effectiveness</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div class="text-center p-3 rounded-lg bg-gray-50">
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total_events'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Events</p>
        </div>
        <div class="text-center p-3 rounded-lg bg-blue-50">
            <p class="text-2xl font-bold text-blue-600 font-jakarta">{{ $data['total_attended'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Hadir</p>
        </div>
        <div class="text-center p-3 rounded-lg bg-emerald-50">
            <p class="text-2xl font-bold text-emerald-600 font-jakarta">{{ $data['converted'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Converted</p>
        </div>
        <div class="text-center p-3 rounded-lg bg-purple-50">
            <p class="text-2xl font-bold text-purple-600 font-jakarta">{{ $data['conversion_rate'] ?? 0 }}%</p>
            <p class="text-xs text-gray-400 mt-1">Conv. Rate</p>
        </div>
    </div>
</div>
