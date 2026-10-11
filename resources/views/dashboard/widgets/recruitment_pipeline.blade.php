@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-xl">person_add</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Recruitment Pipeline</h3>
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
            <p class="text-xs text-gray-400 mb-1">Total Aplikasi</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total_applications'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-3 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Hired</p>
                <p class="text-sm font-semibold text-emerald-600">{{ $data['hired'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Rejected</p>
                <p class="text-sm font-semibold text-red-500">{{ $data['rejected'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Conversion</p>
                <p class="text-sm font-semibold text-blue-600">{{ $data['conversion_rate'] ?? 0 }}%</p>
            </div>
        </div>

        @if(!empty($data['by_status']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Pipeline Status</p>
            @foreach($data['by_status'] as $status => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600">{{ ucfirst($status) }}</span>
                    <span class="text-xs font-semibold text-gray-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
