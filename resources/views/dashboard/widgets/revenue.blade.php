@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600 text-xl">payments</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Revenue</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['trend_percent']))
            <div class="flex items-center gap-1 {{ $data['trend_percent'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                <span class="material-symbols-outlined text-sm">{{ $data['trend_percent'] >= 0 ? 'trending_up' : 'trending_down' }}</span>
                <span class="text-xs font-semibold">{{ abs($data['trend_percent']) }}%</span>
            </div>
        @endif
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Member Revenue</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">Rp {{ number_format($data['member_revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>

        @if(isset($data['payroll_cost']) && $data['payroll_cost'] > 0)
        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Payroll Cost</p>
                <p class="text-sm font-semibold text-gray-600">Rp {{ number_format($data['payroll_cost'], 0, ',', '.') }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
