@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-red-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-red-500 text-xl">account_balance_wallet</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Expense Tracking</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Total Pengeluaran</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">Rp {{ number_format($data['total_expense'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-50">
            <div class="p-2 rounded-lg bg-orange-50">
                <p class="text-xs text-gray-400">Operasional</p>
                <p class="text-sm font-bold text-orange-600">Rp {{ number_format($data['opex'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-2 rounded-lg bg-blue-50">
                <p class="text-xs text-gray-400">Reimbursement</p>
                <p class="text-sm font-bold text-blue-600">Rp {{ number_format($data['reimbursement'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        @if(isset($data['pending_reimbursement_count']) && $data['pending_reimbursement_count'] > 0)
            <div class="flex items-center gap-2 p-2 rounded-lg bg-yellow-50">
                <span class="material-symbols-outlined text-yellow-500 text-base">pending</span>
                <span class="text-xs text-yellow-700">{{ $data['pending_reimbursement_count'] }} reimbursement pending approval</span>
            </div>
        @endif
    </div>
</div>
