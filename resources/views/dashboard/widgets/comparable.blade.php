@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-cyan-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-cyan-600 text-xl">sync_alt</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Comparable</h3>
            <p class="text-xs text-gray-400">{{ $data['current_period'] ?? '-' }} vs {{ $data['prev_period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        @foreach([
            ['label' => 'Revenue', 'curr' => $data['current']['revenue'] ?? 0, 'prev' => $data['previous']['revenue'] ?? 0, 'pct' => $data['growth']['revenue_pct'] ?? 0],
            ['label' => 'Member Baru', 'curr' => $data['current']['new_members'] ?? 0, 'prev' => $data['previous']['new_members'] ?? 0, 'pct' => $data['growth']['members_pct'] ?? 0],
            ['label' => 'Cost', 'curr' => $data['current']['cost'] ?? 0, 'prev' => $data['previous']['cost'] ?? 0, 'pct' => $data['growth']['cost_pct'] ?? 0],
        ] as $row)
            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50">
                <span class="text-xs text-gray-600 w-24">{{ $row['label'] }}</span>
                <span class="text-xs text-gray-400 flex-1 text-center">
                    {{ is_numeric($row['curr']) && $row['curr'] > 1000 ? 'Rp '.number_format($row['curr']/1000000, 1).'M' : $row['curr'] }}
                </span>
                <span class="text-xs font-bold {{ $row['pct'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ $row['pct'] >= 0 ? '+' : '' }}{{ $row['pct'] }}%
                </span>
            </div>
        @endforeach
        @if(isset($data['yoy']))
            <div class="pt-2 border-t border-gray-50 text-xs text-gray-400 text-center">
                YoY Revenue: <strong class="{{ ($data['yoy']['revenue_pct'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ ($data['yoy']['revenue_pct'] ?? 0) >= 0 ? '+' : '' }}{{ $data['yoy']['revenue_pct'] ?? 0 }}%
                </strong>
            </div>
        @endif
    </div>
</div>
