@props(['data'])

@php
    $isPositive = ($data['profit'] ?? 0) >= 0;
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg {{ $isPositive ? 'bg-blue-50' : 'bg-red-50' }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ $isPositive ? 'text-blue-600' : 'text-red-500' }} text-xl">trending_up</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Profit</h3>
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
            <p class="text-xs text-gray-400 mb-1">Net Profit</p>
            <p class="text-2xl font-bold {{ $isPositive ? 'text-gray-900' : 'text-red-600' }} font-jakarta">Rp {{ number_format($data['profit'] ?? 0, 0, ',', '.') }}</p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Margin</p>
                <p class="text-sm font-semibold {{ ($data['margin_percent'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $data['margin_percent'] ?? 0 }}%</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400">Revenue</p>
                <p class="text-sm font-semibold text-gray-600">Rp {{ number_format($data['revenue'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Payroll</p>
                <p class="text-sm font-semibold text-gray-600">Rp {{ number_format($data['payroll_cost'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400">Operational</p>
                <p class="text-sm font-semibold text-gray-600">Rp {{ number_format($data['operational_cost'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
