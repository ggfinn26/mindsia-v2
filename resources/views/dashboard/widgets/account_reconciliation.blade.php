@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-teal-600 text-xl">account_balance</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Account Reconciliation</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Total Inflow</p>
                <p class="text-lg font-bold text-emerald-600 font-jakarta">Rp {{ number_format($data['total_inflow'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Total Outflow</p>
                <p class="text-lg font-bold text-red-500 font-jakarta">Rp {{ number_format($data['total_outflow'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="pt-2 border-t border-gray-50 flex items-center justify-between">
            <p class="text-xs text-gray-400">Net Balance</p>
            <p class="text-sm font-bold {{ ($data['net_balance'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ ($data['net_balance'] ?? 0) >= 0 ? '+' : '' }}Rp {{ number_format($data['net_balance'] ?? 0, 0, ',', '.') }}
            </p>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div class="text-center p-2 rounded-lg bg-yellow-50">
                <p class="text-sm font-bold text-yellow-600">{{ $data['unverified_payments'] ?? 0 }}</p>
                <p class="text-xs text-gray-400">Unverified</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-gray-50">
                <p class="text-sm font-bold text-gray-700">{{ $data['pending_payroll'] ?? 0 }}</p>
                <p class="text-xs text-gray-400">Payroll Pending</p>
            </div>
        </div>
    </div>
</div>
