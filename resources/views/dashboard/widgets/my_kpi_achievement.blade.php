@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-indigo-600 text-xl">military_tech</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">My KPI Achievement</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['grade']))
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">Grade {{ $data['grade'] }}</span>
        @endif
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-1">Total Score</p>
                <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total_score'] ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Evaluasi</p>
                <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total_evaluations'] ?? 0 }}</p>
            </div>
        </div>
        @if(!empty($data['by_category']))
            <div class="pt-2 border-t border-gray-50 space-y-1.5">
                @foreach($data['by_category'] as $cat)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 truncate max-w-[60%]">{{ $cat['category'] }}</span>
                        <span class="text-xs font-semibold text-indigo-600">{{ $cat['avg_score'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
