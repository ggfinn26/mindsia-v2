@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-blue-600 text-xl">bar_chart</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">My Student Progress</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-3 gap-2">
            <div class="text-center p-2 rounded-lg bg-blue-50">
                <p class="text-xl font-bold text-blue-600">{{ $data['total_students'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Total</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-emerald-50">
                <p class="text-xl font-bold text-emerald-600">{{ $data['on_track'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-0.5">On Track</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-red-50">
                <p class="text-xl font-bold text-red-500">{{ $data['behind'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Behind</p>
            </div>
        </div>
        @if(isset($data['avg_completion_rate']))
            <div class="pt-2 border-t border-gray-50">
                <div class="flex justify-between text-xs text-gray-400 mb-1">
                    <span>Rata-rata Progress</span>
                    <span class="font-medium text-blue-600">{{ $data['avg_completion_rate'] }}%</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ min($data['avg_completion_rate'], 100) }}%"></div>
                </div>
            </div>
        @endif
    </div>
</div>
