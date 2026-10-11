@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-cyan-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-cyan-600 text-xl">waterfall_chart</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Cash Flow</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Inflow</p>
                <p class="text-lg font-bold text-emerald-600 font-jakarta">Rp {{ number_format($data['inflow'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Outflow</p>
                <p class="text-lg font-bold text-red-500 font-jakarta">Rp {{ number_format($data['total_outflow'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="pt-2 border-t border-gray-50">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-400">Net Cash Flow</p>
                <p class="text-sm font-bold {{ ($data['net_cash_flow'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ ($data['net_cash_flow'] ?? 0) >= 0 ? '+' : '' }}Rp {{ number_format($data['net_cash_flow'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="pt-1 space-y-1">
            <div class="flex justify-between text-xs text-gray-400">
                <span>Payroll</span>
                <span>Rp {{ number_format($data['payroll_outflow'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs text-gray-400">
                <span>Opex</span>
                <span>Rp {{ number_format($data['opex_outflow'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
