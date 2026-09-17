@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-blue-600 text-xl">summarize</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Financial Reports</h3>
            <p class="text-xs text-gray-400">YTD {{ $data['year'] ?? date('Y') }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-3 gap-2">
            <div class="text-center p-2 rounded-lg bg-emerald-50">
                <p class="text-xs font-bold text-emerald-700">Rp {{ number_format(($data['ytd_revenue'] ?? 0) / 1000000, 1) }}M</p>
                <p class="text-xs text-gray-400 mt-0.5">Revenue</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-red-50">
                <p class="text-xs font-bold text-red-600">Rp {{ number_format(($data['ytd_cost'] ?? 0) / 1000000, 1) }}M</p>
                <p class="text-xs text-gray-400 mt-0.5">Cost</p>
            </div>
            <div class="text-center p-2 rounded-lg {{ ($data['ytd_margin'] ?? 0) >= 0 ? 'bg-blue-50' : 'bg-orange-50' }}">
                <p class="text-xs font-bold {{ ($data['ytd_margin'] ?? 0) >= 0 ? 'text-blue-700' : 'text-orange-700' }}">Rp {{ number_format(($data['ytd_margin'] ?? 0) / 1000000, 1) }}M</p>
                <p class="text-xs text-gray-400 mt-0.5">Margin</p>
            </div>
        </div>
        @if(!empty($data['monthly_trend']))
            <div class="pt-2 border-t border-gray-50">
                <p class="text-xs text-gray-400 mb-2">Tren Bulanan (Rp)</p>
                <div class="space-y-1">
                    @foreach(array_slice($data['monthly_trend'], -3) as $month)
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">{{ $month['month'] }}</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($month['revenue'] / 1000000, 1) }}M</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
