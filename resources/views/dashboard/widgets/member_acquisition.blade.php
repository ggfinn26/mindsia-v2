@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-xl">person_add</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Member Acquisition</h3>
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
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Member Baru</p>
                <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['new_members'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Conversion Rate</p>
                <p class="text-2xl font-bold text-emerald-600 font-jakarta">{{ $data['conversion_rate'] ?? 0 }}%</p>
            </div>
        </div>

        @if(!empty($data['pipeline']))
            <div class="pt-2 border-t border-gray-50">
                <p class="text-xs text-gray-400 mb-2">Pipeline</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($data['pipeline'] as $status => $count)
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                            {{ ucfirst($status) }}: <strong>{{ $count }}</strong>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
