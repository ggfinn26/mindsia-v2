@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-indigo-600 text-xl">analytics</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Marketing KPI Snapshot</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Avg Score</p>
                <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['avg_score'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Dievaluasi</p>
                <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total_employees_evaluated'] ?? 0 }}</p>
            </div>
        </div>

        @if(!empty($data['by_grade']))
            <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-50">
                @foreach($data['by_grade'] as $grade => $count)
                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 font-medium">
                        Grade {{ $grade }}: {{ $count }}
                    </span>
                @endforeach
            </div>
        @endif

        @if(!empty($data['top_ranking']))
            <div class="pt-2 border-t border-gray-50">
                <p class="text-xs text-gray-400 mb-2">Top Performer</p>
                @foreach(array_slice($data['top_ranking'], 0, 3) as $i => $item)
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-gray-400 w-4">{{ $i + 1 }}</span>
                        <span class="text-xs text-gray-700 flex-1 truncate">{{ $item['employee'] }}</span>
                        <span class="text-xs font-semibold text-indigo-600">{{ $item['total_score'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
