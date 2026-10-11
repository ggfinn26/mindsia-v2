@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-purple-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-purple-600 text-xl">trending_up</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">My Sales Performance</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Total Member</p>
                <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total_member'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Achievement</p>
                <p class="text-2xl font-bold {{ ($data['achievement_percent'] ?? 0) >= 100 ? 'text-emerald-600' : 'text-yellow-500' }} font-jakarta">
                    {{ $data['achievement_percent'] ?? 0 }}%
                </p>
            </div>
        </div>
        @if(isset($data['target_member']))
            <div class="pt-2 border-t border-gray-50">
                <div class="flex justify-between text-xs text-gray-400 mb-1">
                    <span>Progress ke Target</span>
                    <span>{{ $data['total_member'] ?? 0 }} / {{ $data['target_member'] }}</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ ($data['achievement_percent'] ?? 0) >= 100 ? 'bg-emerald-500' : 'bg-purple-500' }}"
                         style="width: {{ min($data['achievement_percent'] ?? 0, 100) }}%"></div>
                </div>
            </div>
        @endif
    </div>
</div>
