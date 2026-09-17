@props(['data'])

@php
    $utilization = $data['utilization_percent'] ?? 0;
    $isOverBudget = $utilization > 100;
    $barColor = $isOverBudget ? 'bg-red-500' : ($utilization > 80 ? 'bg-amber-500' : 'bg-emerald-500');
    $categoryLabels = [
        'sewa' => 'Sewa', 'listrik' => 'Listrik', 'air' => 'Air',
        'internet' => 'Internet', 'gaji_non_employee' => 'Gaji Non-Employee',
        'peralatan' => 'Peralatan', 'kebersihan' => 'Kebersihan',
        'keamanan' => 'Keamanan', 'lainnya' => 'Lainnya',
    ];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg {{ $isOverBudget ? 'bg-red-50' : 'bg-violet-50' }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ $isOverBudget ? 'text-red-500' : 'text-violet-600' }} text-xl">account_balance_wallet</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Cost Control</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['pending_budget_count']) && $data['pending_budget_count'] > 0)
            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                {{ $data['pending_budget_count'] }} draft
            </span>
        @endif
    </div>

    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400">Budget</p>
                <p class="text-lg font-bold text-gray-900 font-jakarta">Rp {{ number_format($data['budget_total'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400">Actual</p>
                <p class="text-lg font-bold {{ $isOverBudget ? 'text-red-600' : 'text-gray-900' }} font-jakarta">Rp {{ number_format($data['actual_total'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-400">Utilization</span>
                <span class="text-xs font-semibold {{ $isOverBudget ? 'text-red-500' : 'text-gray-600' }}">{{ $utilization }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ min($utilization, 100) }}%"></div>
            </div>
        </div>

        @if(isset($data['variance']))
        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-gray-400">Variance</span>
            <span class="text-sm font-semibold {{ $data['variance'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ $data['variance'] >= 0 ? '+' : '' }}Rp {{ number_format($data['variance'], 0, ',', '.') }}
            </span>
        </div>
        @endif

        @if(!empty($data['cost_by_category']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Cost Breakdown</p>
            <div class="space-y-1">
                @foreach($data['cost_by_category'] as $category => $amount)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $categoryLabels[$category] ?? ucfirst($category) }}</span>
                        <span class="text-xs font-medium text-gray-700">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
