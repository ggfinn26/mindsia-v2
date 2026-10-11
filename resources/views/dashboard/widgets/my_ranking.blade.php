@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-yellow-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-yellow-500 text-xl">emoji_events</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">My Ranking</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-3 gap-2">
        <div class="text-center p-3 rounded-lg bg-yellow-50">
            <p class="text-2xl font-bold text-yellow-500">#{{ $data['branch_rank'] ?? '-' }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Cabang</p>
            <p class="text-xs text-gray-300">dari {{ $data['branch_total'] ?? '-' }}</p>
        </div>
        <div class="text-center p-3 rounded-lg bg-orange-50">
            <p class="text-2xl font-bold text-orange-500">#{{ $data['area_rank'] ?? '-' }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Area</p>
            <p class="text-xs text-gray-300">dari {{ $data['area_total'] ?? '-' }}</p>
        </div>
        <div class="text-center p-3 rounded-lg bg-red-50">
            <p class="text-2xl font-bold text-red-500">#{{ $data['national_rank'] ?? '-' }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Nasional</p>
            <p class="text-xs text-gray-300">dari {{ $data['national_total'] ?? '-' }}</p>
        </div>
    </div>
</div>
