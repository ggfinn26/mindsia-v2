@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-indigo-600 text-xl">workspace_premium</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">My Performance Summary</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['grade']))
            <span class="text-sm font-bold px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700">Grade {{ $data['grade'] }}</span>
        @endif
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div class="text-center p-3 rounded-lg bg-indigo-50">
                <p class="text-2xl font-bold text-indigo-600">{{ $data['total_score'] ?? '-' }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Total Score</p>
            </div>
            <div class="text-center p-3 rounded-lg bg-gray-50">
                <p class="text-2xl font-bold text-gray-700">{{ $data['rank_branch'] ? '#'.$data['rank_branch'] : '-' }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Rank Cabang</p>
            </div>
        </div>
        @if(!empty($data['evaluations']))
            <div class="pt-2 border-t border-gray-50 space-y-1">
                @foreach(array_slice($data['evaluations'], 0, 3) as $eval)
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500 truncate max-w-[60%]">{{ $eval['category'] }}</span>
                        <span class="font-semibold text-indigo-600">{{ $eval['score'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
