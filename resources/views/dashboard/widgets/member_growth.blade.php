@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-sky-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-sky-600 text-xl">group_add</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Member Growth</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Member Aktif</p>
                <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total_active'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Growth YoY</p>
                <p class="text-2xl font-bold {{ ($data['yoy_growth'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }} font-jakarta">
                    {{ ($data['yoy_growth'] ?? 0) >= 0 ? '+' : '' }}{{ $data['yoy_growth'] ?? 0 }}%
                </p>
            </div>
        </div>
        @if(!empty($data['monthly_trend']))
            <div class="pt-2 border-t border-gray-50">
                <p class="text-xs text-gray-400 mb-2">Tren 6 Bulan</p>
                <div class="space-y-1">
                    @foreach(array_slice($data['monthly_trend'], -6) as $month)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500 w-12">{{ $month['month'] }}</span>
                            <div class="flex-1 mx-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                @php $max = max(array_column($data['monthly_trend'], 'count')) ?: 1; @endphp
                                <div class="h-full bg-sky-400 rounded-full" style="width: {{ ($month['count'] / $max) * 100 }}%"></div>
                            </div>
                            <span class="font-semibold text-gray-800 w-8 text-right">{{ $month['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
