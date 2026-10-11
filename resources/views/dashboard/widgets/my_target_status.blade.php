@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-amber-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-amber-600 text-xl">flag</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">My Target Status</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Target</p>
                <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['target'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Actual</p>
                <p class="text-2xl font-bold {{ ($data['actual'] ?? 0) >= ($data['target'] ?? 0) ? 'text-emerald-600' : 'text-amber-600' }} font-jakarta">{{ $data['actual'] ?? 0 }}</p>
            </div>
        </div>
        <div class="pt-2 border-t border-gray-50">
            <div class="flex justify-between text-xs text-gray-400 mb-1">
                <span>Progress</span>
                <span class="font-medium {{ ($data['achievement_percent'] ?? 0) >= 100 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $data['achievement_percent'] ?? 0 }}%</span>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ ($data['achievement_percent'] ?? 0) >= 100 ? 'bg-emerald-500' : 'bg-amber-500' }}"
                     style="width: {{ min($data['achievement_percent'] ?? 0, 100) }}%"></div>
            </div>
        </div>
        @if(isset($data['sisa_target']) && $data['sisa_target'] > 0)
            <p class="text-xs text-gray-400 text-center">Butuh <strong class="text-gray-700">{{ $data['sisa_target'] }}</strong> lagi untuk capai target</p>
        @endif
    </div>
</div>
